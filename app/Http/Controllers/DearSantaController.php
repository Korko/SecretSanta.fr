<?php

namespace App\Http\Controllers;

use App\DearSanta;
use App\Http\Requests\DearSantaRequest;
use App\Mail\DearSanta as DearSantaEmail;
use App\Mail as MailModel;
use App\Participant;
use Hashids;
use Mail;
use Metrics;

class DearSantaController extends Controller
{
    public function view(Participant $participant)
    {
        return view('dearSanta', [
            'santa' => Hashids::encode($participant->id),
        ]);
    }

    public function fetch(Participant $participant)
    {
        return response()->json([
            'santa' => [
                'id' => Hashids::encode($participant->id),
                'name' => $participant->name,
            ],
            'draw' => $participant->draw->mail_title,
            'organizer' => $participant->draw->organizer->name,
            'emails' => $participant->dearSanta->with('mail')->mapWithKeys(function ($email) {
                return [$email->id => $email->only(['id', 'mail_body', 'mail'])];
            }),
        ]);
    }

    public function fetchState(Participant $participant)
    {
        return response()->json([
            'emails' => $participant->dearSanta->with('mail')->mapWithKeys(function ($email) {
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
            redirect('/dearsanta/'.$participant->id)->with('message', $message);
    }

    protected function sendMail(DearSanta $dearSanta)
    {
        Metrics::increment('dearsanta');

        Mail::to([['email' => $dearSanta->sender->santa->address, 'name' => $dearSanta->sender->santa->name]])
            ->queue(new DearSantaEmail($dearSanta));
    }
}
