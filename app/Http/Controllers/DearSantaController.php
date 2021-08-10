<?php

namespace App\Http\Controllers;

use App\Http\Requests\DearSantaRequest;
use App\Models\DearSanta;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use App\Notifications\DearSanta as DearSantaNotification;
use Illuminate\Support\Facades\URL;

class DearSantaController extends Controller
{
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
            'draw' => $participant->draw->mail_title,
            'organizer' => $participant->draw->organizer->name,
            'emails' => $participant->dearSantas->mapWithKeys(function ($email) {
                return [$email->id => $email->only(['id', 'mail_body', 'mail'])];
            }),
            'resendEmailUrls' => $participant->dearSantas->mapWithKeys(function ($dearSanta) use ($participant) {
                return [
                    $dearSanta->id => URL::signedRoute('dearSanta.resend', [
                        'participant' => $participant, 'dearSanta' => $dearSanta
                    ])
                ];
            }),
        ]);
    }

    public function fetchState(Participant $participant)
    {
        return response()->json([
            'emails' => $participant->dearSantas->mapWithKeys(function ($dearSanta) {
                return [$dearSanta->id => $dearSanta->only(['id', 'mail_body', 'mail'])];
            }),
        ]);
    }

    public function resend(Participant $participant, DearSanta $dearSanta, \Illuminate\Http\Request $request)
    {
        abort_unless($dearSanta->mail->delivery_status === MailModel::ERROR, 403, 'Cannot resend a non failed email');

        $participant->createMetric('resend_email');

        $participant->santa->notify(new DearSantaNotification($dearSanta));

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json([
                'message' => $message, 'email' => $dearSanta->only([
                    'id', 'mail_body', 'mail',
                ]),
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
                'message' => $message, 'email' => $dearSanta->only([
                    'id', 'mail_body', 'mail',
                ]),
            ]) :
            redirect('/dearSanta/'.$participant->hash)->with('message', $message);
    }
}
