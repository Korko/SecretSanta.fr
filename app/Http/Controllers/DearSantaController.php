<?php

namespace App\Http\Controllers;

use App\Http\Requests\DearSantaRequest;
use App\Mail\DearSanta;
use App\Participant;
use Hashids;
use Mail;
use Metrics;

class DearSantaController extends Controller
{
    public function view(Participant $participant)
    {
        return view('dearSanta', [
            'santa'     => Hashids::encode($participant->id),
        ]);
    }

    public function fetch(Participant $participant)
    {
        return [
            'santa' => $participant->name,
            'draw' => $participant->draw->only(['id', 'email_title']),
            'organizer' => $participant->draw->organizer->name,
        ];
    }

    public function handle(Participant $participant, DearSantaRequest $request)
    {
        Metrics::increment('dearsanta');

        $this->sendMail($participant->santa, $request->input('content'));

        $message = trans('message.sent');

        return $request->ajax() ?
            ['message' => $message] :
            redirect('/dearsanta/'.$participant->id)->with('message', $message);
    }

    protected function sendMail(Participant $santa, $content)
    {
        Mail::to([['email' => $santa->email_address, 'name' => $santa->name]])
            ->queue(new DearSanta($santa, $content));
    }
}
