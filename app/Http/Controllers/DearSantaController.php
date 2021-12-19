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

    public function view(Participant $participant)
    {
        return response()->view('dearSanta', [
            'participant' => $participant->hash,
        ]);
    }

    public function fetch(Participant $participant)
    {
        // The hash was validated in middleware so we can validate that the email was received
        $participant->mail->markAsReceived();

        return response()->json([
            'participant' => $participant->only(['hash', 'name']),
            'draw' => $participant->draw->only(['hash', 'mail_title', 'created_at', 'expires_at', 'organizer_name']),
            'targetDearSantaLastUpdate' => $participant->target->dearSantas->load('mail')->max('mail.updated_at'),
            'emails' => $participant->dearSantas->mapWithKeys(function ($email) {
                return [
                    $email->mail->id => $email->only($this->dearSantaPublicFields)
                ];
            }),
            'resendEmailUrls' => $participant->dearSantas->mapWithKeys(function ($dearSanta) use ($participant) {
                return [
                    $dearSanta->mail->id => URL::signedRoute('dearSanta.resend', [
                        'participant' => $participant, 'dearSanta' => $dearSanta
                    ])
                ];
            }),
            'resendTargetEmailsUrl' => URL::signedRoute('dearSanta.resend_target', [
                'participant' => $participant
            ])
        ]);
    }

    public function fetchState(Participant $participant)
    {
        return response()->json([
            'emails' => $participant->dearSantas->mapWithKeys(function ($dearSanta) {
                return [
                    $dearSanta->mail->id => $dearSanta->only($this->dearSantaPublicFields)
                ];
            }),
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
                redirect('/dearSanta/'.$participant->hash)->with('message', $message);
        } catch(Exception $e) {
            $error = trans('error.email');

            return $request->ajax() ?
                response()->json([
                    'error' => $error,
                    'email' => $dearSanta->refresh()->only($this->dearSantaPublicFields),
                ]) :
                redirect('/dearSanta/'.$participant->hash)->with('error', $error);
        }
    }

    public function resendTarget(Participant $participant, \Illuminate\Http\Request $request)
    {
        $dearSantas = $participant->dearSantas->load('mail');
        $max = $dearSantas->max('mail.updated_at');
        abort_unless($max !== null && $max->diffInSeconds(Carbon::now()) >= config('mail.resend_delay'), 403, Lang::get('error.resend'));

        $dearSantas->each(function ($dearSanta) use ($participant) {
            $participant->notify(new DearSantaNotification($dearSanta));
        });

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json([
                'message' => $message,
            ]) :
            redirect('/dearSanta/'.$participant->hash)->with('message', $message);
    }

    public function handle(Participant $participant, DearSantaRequest $request)
    {
        $dearSanta = new DearSanta();
        $dearSanta->sender()->associate($participant);
        $dearSanta->mail_body = $request->input('content');
        $dearSanta->save();

        $participant->createMetric('dearSanta');

        $participant->santa->notify(new DearSantaNotification($dearSanta));

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json([
                'message' => $message,
                'email' => DearSanta::find($dearSanta->id)->only($this->dearSantaPublicFields),
            ]) :
            redirect('/dearSanta/'.$participant->hash)->with('message', $message);
    }
}
