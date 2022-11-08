<?php

namespace App\Http\Controllers;

use App\Actions\SendMessageToSanta;
use App\Actions\SendTargetToParticipant;
use App\Enums\QuestionToSanta;
use App\Http\Requests\DearSantaRequest;
use App\Http\Requests\DearTargetRequest;
use App\Models\DearSanta;
use App\Models\DearTarget;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Lang;

class DearSantaController extends Controller
{
    protected $dearSantaPublicFields = ['id', 'mail_body', 'mail', 'created_at', 'updated_at'];

    protected $dearTargetPublicFields = ['id', 'mail_type', 'mail_body', 'mail', 'created_at', 'updated_at'];

    public function index(Participant $participant)
    {
        return response()->inertia('DearSanta', [
            'routes' => [
                'contactSanta' => URL::signedRoute('santa.contactSanta', ['participant' => $participant]),
                'fetch' => URL::signedRoute('santa.fetch', ['participant' => $participant]),
                'resendTarget' => URL::signedRoute('santa.resendTarget', ['participant' => $participant]),
            ],
            'dearTargetTypes' => QuestionToSanta::cases(),
        ]);
    }

    /**
     * Return encrypted data
     */
    public function fetch(Participant $participant)
    {
        // The hash was validated in middleware so we can validate that the email was received
        $participant->mail->markAsReceived();

        $participant->load(
            'target', 'target.dearSantas', 'target.dearSantas.mail', 'dearSantas', 'dearSantas.mail',
            'santa', 'santa.dearTargets', 'santa.dearTargets.mail', 'dearTargets', 'dearTargets.mail'
        );

        return response()->json([
            'participant' => $participant->only(['hash', 'name']),
            'target' => $participant->target->only(['name']) + [
                'contactUrl' => URL::signedRoute('santa.contactTarget', ['participant' => $participant, 'target' => $participant->target]),
            ],
            'draw' => $participant->draw->only(['hash', 'mail_title', 'created_at', 'finished_at', 'organizer_name']),
            'targetDearSantaLastUpdate' => $participant->target->dearSantas->max('mail.updated_at') ?: Carbon::now(),
            'santaDearTargetLastUpdate' => $participant->santa->dearTargets->max('mail.updated_at') ?: Carbon::now(),
            'dearSantas' => $participant->dearSantas->mapWithKeys(function ($email) use ($participant) {
                return [
                    $email->mail->id => $email->only($this->dearSantaPublicFields) + [
                        'resendUrl' => URL::signedRoute('santa.resendDearSanta', [
                            'participant' => $participant, 'dearSanta' => $email,
                        ]),
                    ],
                ];
            }),
            'dearTargets' => $participant->dearTargets->mapWithKeys(function ($email) use ($participant) {
                return [
                    $email->mail->id => $email->only($this->dearTargetPublicFields) + [
                        'resendUrl' => URL::signedRoute('santa.resendDearTarget', [
                            'participant' => $participant, 'dearTarget' => $email,
                        ]),
                    ],
                ];
            }),
        ]);
    }

    public function resendDearSanta(Participant $participant, DearSanta $dearSanta)
    {
        abort_unless($dearSanta->mail->updated_at->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

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

    public function resendDearTarget(Participant $participant, DearTarget $dearTarget)
    {
        abort_unless($dearTarget->mail->updated_at->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

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

    public function resendTarget(Participant $participant)
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

    public function handleSanta(Participant $participant, DearSantaRequest $request)
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

    public function handleTarget(Participant $participant, DearTargetRequest $request)
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
