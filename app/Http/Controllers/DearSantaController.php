<?php

namespace App\Http\Controllers;

use App\DearSanta;
use App\Http\Requests\DearSantaRequest;
use App\Mail\DearSanta as DearSantaEmail;
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
            'draw' => $participant->draw->email_title,
            'organizer' => $participant->draw->organizer->name,
            'emails' => $participant->dearSanta->mapWithKeys(function ($email) {
                return [$email->id => $email->only(['id', 'email_body', 'delivery_status', 'created_at', 'updated_at'])];
            }),
        ]);
    }

    public function fetchState(Participant $participant)
    {
        return response()->json([
            'emails' => $participant->dearSanta->mapWithKeys(function ($email) {
                return [$email->id => $email->only(['id', 'email_body', 'delivery_status', 'created_at', 'updated_at'])];
            }),
        ]);
    }

    public function handle(Participant $participant, DearSantaRequest $request)
    {
        $dearSanta = new DearSanta();
        $dearSanta->sender()->associate($participant);
        $dearSanta->email_body = $request->input('content');
        $dearSanta->save();

        $this->sendMail($dearSanta);

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json([
                'message' => $message, 'email' => $dearSanta->only([
                    'id', 'email_body', 'delivery_status', 'created_at', 'updated_at',
                ]),
            ]) :
            redirect('/dearsanta/'.$participant->id)->with('message', $message);
    }

    protected function sendMail(DearSanta $dearSanta)
    {
        Metrics::increment('dearsanta');

        Mail::to([['email' => $dearSanta->sender->santa->email_address, 'name' => $dearSanta->sender->santa->name]])
            ->queue(new DearSantaEmail($dearSanta));
    }
}
