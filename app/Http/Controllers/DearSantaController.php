<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ReportReason;
use App\Http\Requests\DearSantaRequest;
use App\Models\DearSanta;
use App\Models\Participant;
use App\Notifications\DearSanta as DearSantaNotification;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

final class DearSantaController extends Controller
{
    // dear_santas has no timestamps: the dates shown come from the related mail
    /** @var list<string> */
    private array $dearSantaPublicFields = ['id', 'mail_body', 'mail'];

    public function view(Participant $participant): Response
    {
        return response()->view('dearSanta', [
            'participant' => $participant->hash,
        ]);
    }

    public function fetch(Participant $participant): JsonResponse
    {
        // The hash was validated in middleware so we can validate that the email was received
        $participant->mail->markAsReceived();

        // Taking part, the organizer got the panel link in this same email
        if ($participant->draw->organizer_received_at === null && $participant->isOrganizer()) {
            $participant->draw->organizer_received_at = now();
            $participant->draw->save();
        }

        return response()->json([
            'participant' => $participant->only(['hash', 'name']),
            'targetDearSantaLastUpdate' => $participant->target->dearSantas->load('mail')->max('mail.updated_at'),
            'channel' => $participant->broadcastChannel(),
            'organizer' => $participant->draw->organizer_name,
            'targetsRevealed' => $participant->draw->targets_revealed_at !== null,
            'report' => $participant->report,
            'isOrganizer' => $participant->isOrganizer(),
            'emails' => $participant->dearSantas->mapWithKeys(fn ($email): array => [
                $email->mail->id => $email->only($this->dearSantaPublicFields),
            ]),
            'resendEmailUrls' => $participant->dearSantas->mapWithKeys(fn ($dearSanta): array => [
                $dearSanta->mail->id => URL::signedRoute('dearSanta.resend', [
                    'participant' => $participant, 'dearSanta' => $dearSanta,
                ]),
            ]),
            'resendTargetEmailsUrl' => URL::signedRoute('dearSanta.resend_target', [
                'participant' => $participant,
            ]),
        ]);
    }

    // "Not me" / "not taking part": shown to the organizer on their panel, null withdraws the report
    public function report(Request $request, Participant $participant): JsonResponse
    {
        // The organizer has their panel (and can delete the draw), reporting to themselves makes no sense
        abort_if($participant->isOrganizer(), 403);

        $request->validate(['reason' => ['nullable', Rule::enum(ReportReason::class)]]);

        $participant->report = $request->enum('reason', ReportReason::class);
        $participant->save();
        $participant->draw->refuseEmail($participant->email, $participant->report !== null);

        $participant->createMetric($participant->report ? 'report_'.$participant->report->value : 'report_cancelled');

        return response()->json(['report' => $participant->report]);
    }

    public function fetchState(Participant $participant): JsonResponse
    {
        return response()->json([
            'emails' => $participant->dearSantas->mapWithKeys(fn ($dearSanta): array => [
                $dearSanta->mail->id => $dearSanta->only($this->dearSantaPublicFields),
            ]),
        ]);
    }

    public function resend(Participant $participant, DearSanta $dearSanta, Request $request): JsonResponse|RedirectResponse
    {
        abort_unless(($dearSanta->mail->updated_at?->diffInSeconds(Date::now()) ?? PHP_INT_MAX) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        $dearSanta->mail->markAsCreated();

        $participant->createMetric('resend_email')->addTag('by', 'santa');

        try {
            $participant->santa->notify(new DearSantaNotification($dearSanta));

            $message = trans('message.sent');

            return $request->ajax() ?
                response()->json([
                    'message' => $message,
                    'email' => $dearSanta->refresh()->only($this->dearSantaPublicFields),
                ]) :
                redirect('/dearSanta/'.$participant->hash)->with('message', $message);
        } catch (Exception) {
            $error = trans('error.email');

            return $request->ajax() ?
                response()->json([
                    'error' => $error,
                    'email' => $dearSanta->refresh()->only($this->dearSantaPublicFields),
                ]) :
                redirect('/dearSanta/'.$participant->hash)->with('error', $error);
        }
    }

    public function resendTarget(Participant $participant, Request $request): JsonResponse|RedirectResponse
    {
        $dearSantas = $participant->dearSantas->load('mail');
        $max = $dearSantas->max('mail.updated_at');
        abort_unless($max instanceof CarbonImmutable && $max->diffInSeconds(Date::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        $dearSantas->each(function (DearSanta $dearSanta) use ($participant): void {
            $participant->notify(new DearSantaNotification($dearSanta));
        });

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json([
                'message' => $message,
            ]) :
            redirect('/dearSanta/'.$participant->hash)->with('message', $message);
    }

    public function handle(Participant $participant, DearSantaRequest $request): JsonResponse|RedirectResponse
    {
        $dearSanta = new DearSanta;
        $dearSanta->sender()->associate($participant);
        $dearSanta->mail_body = $request->string('content')->toString();
        $dearSanta->save();

        $participant->createMetric('dearSanta');

        $participant->santa->notify(new DearSantaNotification($dearSanta));

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json([
                'message' => $message,
                'email' => $dearSanta->fresh()?->only($this->dearSantaPublicFields),
            ]) :
            redirect('/dearSanta/'.$participant->hash)->with('message', $message);
    }
}
