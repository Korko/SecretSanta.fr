<?php

namespace App\Http\Controllers;

use App\Enums\QuestionToSanta;
use App\Http\Requests\DearSantaRequest;
use App\Http\Requests\DearTargetRequest;
use App\Models\DearSanta;
use App\Models\DearTarget;
use App\Models\Participant;
use App\Notifications\DearSanta as DearSantaNotification;
use App\Notifications\DearTarget as DearTargetNotification;
use App\Notifications\TargetDrawn as TargetDrawnNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\URL;
use Lang;

class DearSantaController extends Controller
{
    protected $dearSantaPublicFields = ['id', 'mail_body', 'mail', 'created_at', 'updated_at'];
    protected $dearTargetPublicFields = ['id', 'mail_type', 'mail_body', 'mail', 'created_at', 'updated_at'];

    public function index(Participant $participant)
    {
        return static::renderWithInertia('DearSanta.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
                'contactSanta' => URL::signedRoute('santa.contactSanta', ['participant' => $participant]),
                'fetch' => URL::signedRoute('santa.fetch', ['participant' => $participant]),
                'resendTarget' => URL::signedRoute('santa.resendTarget', ['participant' => $participant])
            ],
            'dearTargetTypes' => QuestionToSanta::cases()
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
                            'participant' => $participant, 'dearSanta' => $email
                        ])
                    ]
                ];
            }),
            'dearTargets' => $participant->dearTargets->mapWithKeys(function ($email) use ($participant) {
                return [
                    $email->mail->id => $email->only($this->dearTargetPublicFields) + [
                        'resendUrl' => URL::signedRoute('santa.resendDearTarget', [
                            'participant' => $participant, 'dearTarget' => $email
                        ])
                    ]
                ];
            }),
        ]);
    }

    public function resendDearSanta(Participant $participant, DearSanta $dearSanta, \Illuminate\Http\Request $request)
    {
        abort_unless($dearSanta->mail->updated_at->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        $dearSanta->mail->markAsCreated();

        $participant->createMetric('resend_email');

        try {
            $dearSanta->target->notify(new DearSantaNotification($dearSanta));

            $message = trans('message.sent');

            return $request->ajax() ?
                response()->json([
                    'message' => $message,
                    'email' => $dearSanta->refresh()->only($this->dearSantaPublicFields),
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('message', $message);
        } catch(Exception $e) {
            $error = trans('error.email');

            return $request->ajax() ?
                response()->json([
                    'error' => $error,
                    'email' => $dearSanta->refresh()->only($this->dearSantaPublicFields),
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('error', $error);
        }
    }

    public function resendDearTarget(Participant $participant, DearTarget $dearTarget, \Illuminate\Http\Request $request)
    {
        abort_unless($dearTarget->mail->updated_at->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        $dearTarget->mail->markAsCreated();

        $participant->createMetric('resend_email');

        try {
            $dearTarget->target->notify(new DearTargetNotification($dearTarget));

            $message = trans('message.sent');

            return $request->ajax() ?
                response()->json([
                    'message' => $message,
                    'email' => $dearTarget->refresh()->only($this->dearTargetPublicFields),
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('message', $message);
        } catch(Exception $e) {
            $error = trans('error.email');

            return $request->ajax() ?
                response()->json([
                    'error' => $error,
                    'email' => $dearTarget->refresh()->only($this->dearTargetPublicFields),
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('error', $error);
        }
    }

    public function resendTarget(Participant $participant, \Illuminate\Http\Request $request)
    {
        abort_unless($participant->mail->updated_at->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        $participant->mail->markAsCreated();

        $participant->createMetric('resend_email');

        try {
            $participant->notify(new TargetDrawnNotification);

            $message = trans('message.sent');

            return $request->ajax() ?
                response()->json([
                    'message' => $message,
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('message', $message);
        } catch(Exception $e) {
            $error = trans('error.email');

            return $request->ajax() ?
                response()->json([
                    'error' => $error,
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('error', $error);
        }
    }

    public function handleSanta(Participant $participant, DearSantaRequest $request)
    {
        $dearSanta = new DearSanta();
        $dearSanta->draw()->associate($participant->draw);
        $dearSanta->sender()->associate($participant);
        $dearSanta->mail_body = $request->input('content');
        $dearSanta->save();

        $participant->createMetric('dearSanta');

        try {
            $dearSanta->target->notify(new DearSantaNotification($dearSanta));

            $message = trans('message.sent');

            return $request->ajax() ?
                response()->json([
                    'message' => $message,
                    'email' => $dearSanta->refresh()->only($this->dearSantaPublicFields),
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('message', $message);
        } catch(Exception $e) {
            $error = trans('error.email');

            return $request->ajax() ?
                response()->json([
                    'error' => $error,
                    'email' => $dearSanta->refresh()->only($this->dearSantaPublicFields),
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('error', $error);
        }
    }

    public function handleTarget(Participant $participant, DearTargetRequest $request)
    {
        $dearTarget = new DearTarget();
        $dearTarget->draw()->associate($participant->draw);
        $dearTarget->sender()->associate($participant);
        $dearTarget->mail_type = $request->input('type');
        $dearTarget->save();

        $participant->createMetric('dearTarget');

        try {
            $dearTarget->target->notify(new DearTargetNotification($dearTarget));

            $message = trans('message.sent');

            return $request->ajax() ?
                response()->json([
                    'message' => $message,
                    'email' => $dearTarget->refresh()->only($this->dearTargetPublicFields),
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('message', $message);
        } catch(Exception $e) {
            $error = trans('error.email');

            return $request->ajax() ?
                response()->json([
                    'error' => $error,
                    'email' => $dearTarget->refresh()->only($this->dearTargetPublicFields),
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('error', $error);
        }
    }
}
