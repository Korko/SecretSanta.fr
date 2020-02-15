<?php

namespace App\Http\Controllers;

use App\Draw;
use App\Http\Requests\OrganizerChangeEmailRequest;
use App\Http\Requests\OrganizerResendEmailRequest;
use App\Mail\TargetDrawn;
use App\Participant;
use Mail;
use Metrics;

class OrganizerController extends Controller
{
    public function view(Draw $draw)
    {
        return view('organizer', [
            'draw'         => $draw->id,
        ]);
    }

    public function fetch(Draw $draw)
    {
        return [
            'draw' => $draw->id,
            'participants' => $draw->participants->map(function ($participant) {
                return $participant->only(['id', 'name', 'email_address', 'delivery_status']);
            }),
        ];
    }

    public function changeEmail(OrganizerChangeEmailRequest $request, Draw $draw, Participant $participant)
    {
        $participant->email_address = $request->input('email');
        $participant->delivery_status = Participant::CREATED;
        $participant->save();

        $this->doResendEmail($participant);

        $message = trans('organizer.up_and_sent');

        return $request->ajax() ?
            response()->json(['message' => $message, 'status' => $participant->delivery_status]) :
            redirect('/')->with('message', $message);
    }

    public function resendEmail(OrganizerResendEmailRequest $request, Draw $draw, Participant $participant)
    {
        $participant->delivery_status = Participant::CREATED;
        $participant->save();

        $this->doResendEmail($participant);

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json(['message' => $message, 'status' => $participant->delivery_status]) :
            redirect('/')->with('message', $message);
    }

    protected function doResendEmail(Participant $participant)
    {
        Metrics::increment('email');

        Mail::to([['email' => $participant->email_address, 'name' => $participant->name]])
            ->queue(new TargetDrawn($participant));
    }
}
