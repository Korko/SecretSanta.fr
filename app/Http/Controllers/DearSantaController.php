<?php

namespace App\Http\Controllers;

use App\Http\Requests\DearSantaRequest;
use App\Models\DearSanta;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use App\Notifications\DearSanta as DearSantaNotification;
use App\Traits\UpdatesMailDelivery;

class DearSantaController extends Controller
{
    use UpdatesMailDelivery;

    public function view(Participant $participant)
    {
        return response()->view('dearSanta', [
            'participant' => $participant->hash,
        ]);
    }

    public function fetch(Participant $participant)
    {
        // The hash was validated in middleware so we can validate that the email was received
        $this->updateDelivery($participant->mail, MailModel::RECEIVED);

        return response()->json([
            'participant' => $participant->only(['hash', 'name']),
            'draw' => $participant->draw->mail_title,
            'organizer' => $participant->draw->organizer->name,
            'emails' => $participant->dearSanta->mapWithKeys(function ($email) {
                return [$email->id => $email->only(['id', 'mail_body', 'mail'])];
            }),
        ]);
    }

    public function fetchState(Participant $participant)
    {
        return response()->json([
            'emails' => $participant->dearSanta->mapWithKeys(function ($email) {
                return [$email->id => $email->only(['id', 'mail_body', 'mail'])];
            }),
        ]);
    }

    public function handle(Participant $participant, DearSantaRequest $request)
    {
        $mail = (new MailModel())->draw()->associate($participant->draw);
        $mail->save();

        $dearSanta = new DearSanta();
        $dearSanta->sender()->associate($participant);
        $dearSanta->mail_body = $request->input('content');
        $dearSanta->mail()->associate($mail);
        $dearSanta->save();

        $participant->createMetric('dearsanta');

        $participant->santa->notify(new DearSantaNotification($dearSanta));

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json([
                'message' => $message, 'email' => $dearSanta->only([
                    'id', 'mail_body', 'mail',
                ]),
            ]) :
            redirect('/dearsanta/'.$participant->hash)->with('message', $message);
    }
}
