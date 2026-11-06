<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OrganizerChangeEmailRequest;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\DearSanta;
use App\Notifications\TargetChanged;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

final class OrganizerController extends Controller
{
    public function view(Draw $draw): Response
    {
        return response()->view('organizer', [
            'draw' => $draw->hash,
        ]);
    }

    public function fetch(Draw $draw)
    {
        return response()->json([
            'draw' => $draw->hash,
            'expires_at' => $draw->expires_at,
            'deleted_at' => $draw->deleted_at,
            'participants' => $draw->participants->load('mail')->mapWithKeys(fn ($participant): array => [
                $participant->hash => $participant->only([
                    'hash', 'name', 'email', 'mail',
                ]),
            ]),
            'changeEmailUrls' => $draw->participants->mapWithKeys(fn ($participant): array => [
                $participant->hash => URL::signedRoute('organizerPanel.changeEmail', [
                    'draw' => $participant->draw, 'participant' => $participant,
                ]),
            ]),
            'withdrawalUrls' => $draw->participants->mapWithKeys(fn ($participant): array => [
                $participant->hash => URL::signedRoute('organizerPanel.withdraw', [
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

    public function fetchState(Draw $draw): JsonResponse
    {
        return response()->json([
            'participants' => $draw->participants->load('mail')->mapWithKeys(fn ($participant): array => [$participant->hash => $participant->only(['hash', 'mail'])]),
        ]);
    }

    public function changeEmail(OrganizerChangeEmailRequest $request, Draw $draw, Participant $participant): JsonResponse
    {
        if ($participant->email === $request->input('email')) {
            $message = trans('message.sent');

            $participant->createMetric('resend_email');
        } else {
            // The organizer fixing their own address: keep the draw in sync (recap in their target email, /fix)
            if ($participant->isOrganizer()) {
                $participant->draw->organizer_email = $request->input('email');
                $participant->draw->save();
            }

            $participant->email = $request->input('email');
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

    public function withdraw(Draw $draw, Participant $participant): JsonResponse
    {
        abort_unless($draw->participants->count() > 3, 403, Lang::get('error.withdraw'));

        $santa = $participant->santa;
        $target = $participant->target;

        if ($santa->isNot($target)) {
            // A -> B -> C => A -> C
            $santa->target()->save($target);
        } else {
            // A <-> B: splice A into another pair, C -> D => C -> A -> D
            $excluded = $santa->exclusions->modelKeys();
            $otherSanta = $draw->participants->first(fn (Participant $c): bool => ! in_array($c->id, [$santa->id, $participant->id], true)
                && ! in_array($c->target->id, $excluded, true)
                && ! $c->exclusions->contains($santa));

            if ($otherSanta === null) {
                return response()->json([
                    'error' => trans('organizer.cannot_withdraw', ['name' => $participant->name]),
                ]);
            }

            $target = $otherSanta->target;
            $santa->target()->save($target);
            $otherSanta->target()->save($santa);
            $otherSanta->notify(new TargetChanged);
            $santa->dearSantas->each(fn ($dearSanta) => $otherSanta->notify(new DearSanta($dearSanta)));
        }

        $santa->notify(new TargetWithdrawn);
        $target->dearSantas->each(function ($dearSanta) use ($santa): void {
            $santa->notify(new DearSanta($dearSanta));
        });
        $participant->delete();

        return response()->json([
            'message' => trans('organizer.withdrawn', ['name' => $participant->name]),
        ]);
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
                    ['# Fichier généré le '.date('d-m-Y').' sur '.config('app.name').' ('.config('app.url').')'],
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
            $participant->hash => $participant->target?->name,
        ])->all();
    }
}
