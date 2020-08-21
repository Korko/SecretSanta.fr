<?php

namespace App\Http\Controllers;

use App\Models\DearSanta;
use App\Http\Requests\DearSantaRequest;
use App\Jobs\SendMail;
use App\Models\Mail as MailModel;
use App\Models\Mail\DearSanta as DearSantaEmail;
use App\Models\Participant;
use Metrics;

class DearSantaController extends Controller
{
    public function view(Participant $participant)
    {
        return view('dearSanta', [
            'participant' => $participant->hash,
        ]);
    }

    public function fetch(Participant $participant)
    {
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
        $dearSanta = new DearSanta();
        $dearSanta->sender()->associate($participant);
        $dearSanta->mail_body = $request->input('content');
        $dearSanta->mail()->associate(MailModel::create());
        $dearSanta->save();

        $this->sendMail($dearSanta);

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json([
                'message' => $message, 'email' => $dearSanta->only([
                    'id', 'mail_body', 'mail',
                ]),
            ]) :
            redirect('/dearsanta/'.$participant->hash)->with('message', $message);
    }

    protected function sendMail(DearSanta $dearSanta)
    {
        Metrics::increment('dearsanta');

        SendMail::dispatch($dearSanta->sender->santa, new DearSantaEmail($dearSanta));
    }
}
