<?php

namespace App\Http\Controllers;

use App\Http\Requests\DearSantaRequest;
use App\Models\DearSanta;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use App\Notifications\DearSanta as DearSantaNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\URL;
use Lang;
use Str;

class DearSantaController extends Controller
{
    protected $dearSantaPublicFields = ['id', 'mail_body', 'mail', 'created_at', 'updated_at'];

    public function index(Participant $participant)
    {
        return static::renderWithInertia('DearSanta.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
                'contact' => URL::signedRoute('santa.contact', ['participant' => $participant]),
                'fetch' => URL::signedRoute('santa.fetch', ['participant' => $participant]),
                'resendTarget' => URL::signedRoute('santa.resendTarget', ['participant' => $participant])
            ]
        ]);
    }

    /**
     * Return encrypted data
     */
    public function fetch(Participant $participant)
    {
        // The hash was validated in middleware so we can validate that the email was received
        $participant->mail->markAsReceived();

        $participant->load('target', 'target.dearSantas', 'target.dearSantas.mail', 'dearSantas', 'dearSantas.mail');

        return response()->json([
            'participant' => $participant->only(['hash', 'name']),
            'draw' => $participant->draw->only(['hash', 'mail_title', 'created_at', 'finished_at', 'organizer_name']),
            'targetDearSantaLastUpdate' => $participant->target->dearSantas->max('mail.updated_at') ?: Carbon::now(),
            'emails' => $participant->dearSantas->mapWithKeys(function ($email) {
                return [
                    $email->mail->id => $email->only($this->dearSantaPublicFields)
                ];
            }),
            'routes' => [
                'resendEmail' => $participant->draw->expired ? [] : $participant->dearSantas->mapWithKeys(function ($dearSanta) use ($participant) {
                    return [
                        $dearSanta->mail->id => URL::signedRoute('santa.resend', [
                            'participant' => $participant, 'dearSanta' => $dearSanta
                        ])
                    ];
                })
            ]
        ]);
    }

    public function resend(Participant $participant, DearSanta $dearSanta, \Illuminate\Http\Request $request)
    {
        abort_unless($dearSanta->mail->updated_at->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        $dearSanta->mail->markAsCreated();

        $participant->createMetric('resend_email');

        try {
            $participant->santa->notify(new DearSantaNotification($dearSanta));

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

    public function resendTarget(Participant $participant, \Illuminate\Http\Request $request)
    {
        $dearSantas = $participant->dearSantas->load('mail');
        $max = $dearSantas->max('mail.updated_at');
        abort_unless($max !== null && $max->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        $errors = 0;
        $dearSantas->each(function ($dearSanta) use ($participant, &$errors) {
            try {
                $participant->notify(new DearSantaNotification($dearSanta));
            } catch(Exception $e) {
                $errors++;
            }
        });

        if($errors === 0) {
            $message = trans('message.sent');

            return $request->ajax() ?
                response()->json([
                    'message' => $message,
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('message', $message);
        } else {
            $error = trans('error.email');

            return $request->ajax() ?
                response()->json([
                    'error' => $error,
                ]) :
                redirect()->route('santa.index', ['participant' => $participant->hash])->with('error', $error);
        }
    }

    public function handle(Participant $participant, DearSantaRequest $request)
    {
        $dearSanta = new DearSanta();
        $dearSanta->draw()->associate($participant->draw);
        $dearSanta->sender()->associate($participant);
        $dearSanta->mail_body = $request->input('content');
        $dearSanta->save();

        $participant->createMetric('dearSanta');

        try {
            $participant->santa->notify(new DearSantaNotification($dearSanta));

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
}
