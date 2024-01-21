<?php

namespace App\Http\Controllers;

use App\Actions\SendMessageToSanta;
use App\Actions\SendMessageToTarget;
use App\Actions\SendTargetToParticipant;
use App\Enums\QuestionToSanta;
use App\Http\Requests\DearSantaRequest;
use App\Http\Requests\DearTargetRequest;
use App\Models\DearSanta;
use App\Models\DearTarget;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Lang;
use Symfony\Component\HttpFoundation\Response;

class DearSantaController extends Controller
{
    protected $dearSantaPublicFields = ['ulid', 'mail_body', 'mail', 'created_at', 'updated_at'];

    protected $dearTargetPublicFields = ['ulid', 'mail_type', 'mail_body', 'mail', 'created_at', 'updated_at'];

    public function index(Participant $participant): Response
    {
        return response()->inertia('DearSanta', [
            'routes' => [
                'contactSanta' => URL::signedRoute('participant.contactSanta', ['participant' => $participant]),
                'fetch' => URL::signedRoute('participant.fetch', ['participant' => $participant]),
                'resendTarget' => URL::signedRoute('participant.resendTarget', ['participant' => $participant]),
            ],
            'dearTargetTypes' => QuestionToSanta::cases(),
        ]);
    }

    public function resendDearSanta(Participant $participant, DearSanta $dearSanta): JsonResponse
    {
        abort_unless($dearSanta->mail->updated_at->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        $dearSanta->setRelation('sender', $participant);

        return response()->jsonTry(
            function () use ($dearSanta) {
                app(SendMessageToSanta::class)->resend($dearSanta);

                $dearSanta->sender->createMetric('resend_dearSanta');

                return [
                    'email' => $dearSanta->refresh()->only($this->dearSantaPublicFields),
                ];
            },
            trans('message.sent'),
            trans('error.email')
        );
    }

    public function resendDearTarget(Participant $participant, DearTarget $dearTarget): JsonResponse
    {
        abort_unless($dearTarget->mail->updated_at->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        $dearTarget->setRelation('sender', $participant);

        return response()->jsonTry(
            function () use ($dearTarget) {
                app(SendMessageToTarget::class)->resend($dearTarget);

                $dearTarget->sender->createMetric('resend_dearTarget');

                return [
                    'email' => $dearTarget->refresh()->only($this->dearTargetPublicFields),
                ];
            },
            trans('message.sent'),
            trans('error.email')
        );
    }

    public function resendTarget(Participant $participant): JsonResponse
    {
        abort_unless($participant->mail->updated_at->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        return response()->jsonTry(
            function () use ($participant) {
                app(SendTargetToParticipant::class)->send($participant);

                $participant->createMetric('resend_target');
            },
            trans('message.sent'),
            trans('error.email')
        );
    }

    public function handleSanta(Participant $participant, DearSantaRequest $request): JsonResponse
    {
        return response()->jsonTry(
            function () use ($participant, $request) {
                $dearSanta = app(SendMessageToSanta::class)->send($participant, $request->input('content'));

                $participant->createMetric('dearSanta');

                return [
                    'email' => $dearSanta->refresh()->only($this->dearSantaPublicFields),
                ];
            },
            trans('message.sent'),
            trans('error.email')
        );
    }

    public function handleTarget(Participant $participant, DearTargetRequest $request): JsonResponse
    {
        return response()->jsonTry(
            function () use ($participant, $request) {
                $dearTarget = app(SendMessageToTarget::class)->send($participant, $request->input('type'));

                $participant->createMetric('dearTarget');

                return [
                    'email' => $dearTarget->refresh()->only($this->dearTargetPublicFields),
                ];
            },
            trans('message.sent'),
            trans('error.email')
        );
    }
}
