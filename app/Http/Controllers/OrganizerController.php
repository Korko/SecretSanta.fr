<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OrganizerChangeEmailRequest;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\TargetDrawn;
use App\Services\ParticipantsEditor;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class OrganizerController extends Controller
{
    public function view(Draw $draw): Response
    {
        return response()->view('organizer', [
            'draw' => $draw->hash,
        ]);
    }

    public function fetch(Draw $draw): JsonResponse
    {
        if ($draw->organizer_received_at === null) {
            $draw->organizer_received_at = now();
            $draw->save();
        }

        return response()->json([
            'draw' => $draw->hash,
            'expires_at' => $draw->expires_at->toDateString(), // event day, whatever the browser timezone
            'deleted_at' => $draw->deleted_at,
            'drawn_at' => $draw->created_at,
            'participants' => $draw->participants->load('mail')->mapWithKeys(fn ($participant): array => [
                $participant->hash => $participant->only([
                    'hash', 'name', 'email', 'mail', 'report',
                ]),
            ]),
            'changeEmailUrls' => $draw->participants->mapWithKeys(fn ($participant): array => [
                $participant->hash => URL::signedRoute('organizerPanel.changeEmail', [
                    'draw' => $participant->draw, 'participant' => $participant,
                ]),
            ]),
            'finalCsvAvailable' => $draw->next_solvable,
            'targetsRevealedAt' => $draw->targets_revealed_at,
            'targets' => $draw->targets_revealed_at ? $this->targets($draw) : null,
        ]);
    }

    // Irreversible and visible to participants (see DearSantaController::fetch)
    public function revealTargets(Draw $draw): JsonResponse
    {
        if ($draw->targets_revealed_at === null) {
            $draw->targets_revealed_at = now();
            $draw->save();

            $draw->createMetric('targets_revealed');
        }

        return response()->json([
            'targetsRevealedAt' => $draw->targets_revealed_at,
            'targets' => $this->targets($draw),
        ]);
    }

    // Organizers often mistype the date or the event moves: it drives expiration (final recap) and deletion
    public function changeDate(Request $request, Draw $draw): JsonResponse
    {
        abort_if($draw->expired, 403, Lang::get('error.expired'));

        $request->validate([
            'expires_at' => ['required', 'date_format:Y-m-d', 'after_or_equal:tomorrow', 'before:+1year'],
        ], [
            'expires_at.required' => __('validation.custom.randomform.expiration.required'),
            'expires_at.after_or_equal' => __('validation.custom.randomform.expiration.min'),
            'expires_at.before' => __('validation.custom.randomform.expiration.max'),
            'expires_at.date_format' => __('validation.custom.randomform.expiration.format'),
        ]);

        $draw->expires_at = CarbonImmutable::parse($request->string('expires_at')->value());
        $draw->save();

        return response()->json([
            'expires_at' => $draw->expires_at->toDateString(), // event day, whatever the browser timezone
            'deleted_at' => $draw->deleted_at,
        ]);
    }

    public function fetchState(Draw $draw): JsonResponse
    {
        return response()->json([
            'participants' => $draw->participants->load('mail')->mapWithKeys(fn ($participant): array => [$participant->hash => $participant->only(['hash', 'mail'])]),
        ]);
    }

    public function changeEmail(OrganizerChangeEmailRequest $request, Draw $draw, Participant $participant): JsonResponse
    {
        throw_if($draw->refusesEmail($request->string('email')->toString()), ValidationException::withMessages(['email' => trans('organizer.report.refused_email')]));

        $email = $request->string('email')->toString();
        if ($participant->email === $email) {
            $message = trans('message.sent');

            $participant->createMetric('resend_email');
        } else {
            // The organizer fixing their own address: keep the draw in sync (recap in their target email, /fix)
            if ($participant->isOrganizer()) {
                $participant->draw->organizer_email = $email;
                $participant->draw->save();
            }

            $participant->email = $email;
            $participant->report = null; // a fixed address answers a "not me" report
            $participant->save();

            $participant->createMetric('change_email');

            $message = trans('organizer.up_and_sent');
        }

        $participant->mail->markAsCreated();

        $participant->notify(new TargetDrawn);

        return response()->json([
            'message' => $message, 'participant' => $participant->only(['hash', 'mail']),
        ]);
    }

    // One button (a single change) or the "several at once" mode: either way, each santa gets at most one email
    public function changeParticipants(Request $request, Draw $draw): JsonResponse
    {
        abort_if($draw->expired, 403, Lang::get('error.expired'));

        $names = $draw->participants->pluck('name')->all();
        $hashes = $draw->participants->pluck('hash')->all();
        $request->validate([
            'remove' => ['array'],
            'remove.*' => ['distinct', Rule::in($hashes)],
            'add' => ['array'],
            'add.*.name' => ['required', 'string', 'distinct', Rule::notIn($names)],
            'add.*.email' => ['required', 'email', function (string $attribute, mixed $value, Closure $fail) use ($draw): void {
                if (is_string($value) && $draw->refusesEmail($value)) {
                    $fail(trans('organizer.report.refused_email'));
                }
            }],
        ], [
            'add.*.name.required' => __('validation.custom.randomform.participant.name.required'),
            'add.*.name.distinct' => __('validation.custom.randomform.participant.name.distinct'),
            'add.*.name.not_in' => __('validation.custom.randomform.participant.name.distinct'),
            'add.*.email.required' => __('validation.custom.randomform.participant.email.required'),
            'add.*.email.email' => __('validation.custom.randomform.participant.email.format'),
        ]);

        $remove = $draw->participants->whereIn('hash', $request->array('remove'))->values()->all();
        /** @var list<array{name: string, email: string}> $add validated above */
        $add = array_values($request->array('add'));
        new ParticipantsEditor($draw)->apply($remove, $add);

        $draw->createMetric('participants_changed')
            ->addExtra('added', count($add))
            ->addExtra('removed', count($remove));

        return $this->fetch($draw->refresh());
    }

    public function csvInit(Draw $draw): Response
    {
        $draw->createMetric('csv_initial_download');

        return response(
            $draw->initialCsv(),
            200, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function csvFinal(Draw $draw): Response
    {
        abort_unless($draw->expired || $draw->targets_revealed_at !== null, 403, Lang::get('error.expired'));
        abort_unless($draw->next_solvable, 404, Lang::get('error.solvable'));

        $draw->createMetric('csv_final_download');

        return response(
            "\xEF\xBB\xBF".// UTF-8 BOM
            $draw->participants
                ->appendTargetToExclusions()
                ->toCsv(['name', 'email', 'exclusionsNames'])
                ->prepend([
                    ['# Fichier généré le '.date('d-m-Y').' sur '.config()->string('app.name').' ('.config()->string('app.url').')'],
                    ['# Ce fichier peut être utilisé pour préremplir les participants ainsi que les exclusions associées'],
                ]),
            200, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function delete(Request $request, Draw $draw): JsonResponse
    {
        $draw->delete();

        $draw->createMetric('delete');

        return response()->json([
            'message' => trans('organizer.deleted'),
        ]);
    }

    /** @return array<string, string> participant hash => target name */
    private function targets(Draw $draw): array
    {
        return $draw->participants->load('target')->mapWithKeys(fn (Participant $participant): array => [
            // @phpstan-ignore nullsafe.neverNull (no target yet while participants are being edited)
            $participant->hash => $participant->target?->name,
        ])->all();
    }
}
