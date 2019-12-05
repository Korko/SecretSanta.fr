<?php

namespace App\Http\Controllers;

use App\Draw;
use App\Participant;
use App\Http\Requests\OrganizerChangeEmailRequest;
use App\Http\Requests\OrganizerResendEmailRequest;

class OrganizerController extends Controller
{
    public function view(Draw $draw)
    {
        return view('organizer', [
            'challenge'    => $draw->challenge,
            'draw'         => $draw->id,
            'participants' => $draw->participants->map(function ($participant) {
                return $participant->only(['name', 'email_address', 'delivery_status']);
            }),
        ]);
    }

    public function changeEmail(OrganizerChangeEmailRequest $request, Draw $draw, Participant $participant)
    {
        $participant->email_address = $request->input('email');
        $participant->save();

        $emailSent = $this->doResendEmail($draw, $participant);
        $message = $emailSent ? trans('organizer.up_and_sent') : trans('organizer.up_but_not_sent');

        return $request->ajax() ?
            response()->json(['message' => $message]) :
            redirect('/')->with('message', $message);
    }

    public function resendEmail(OrganizerResendEmailRequest $request, Draw $draw, Participant $participant)
    {
        return true;
    }

    protected function doResendEmail(Draw $draw, Participant $participant)
    {

    }
}
