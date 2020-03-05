<?php

namespace App\Http\Controllers;

use App\Draw;
use App\Http\Requests\OrganizerChangeEmailRequest;
use App\Http\Requests\OrganizerResendEmailRequest;
use App\Mail\TargetDrawn;
use App\Mail as MailModel;
use App\Participant;
use Mail;
use Metrics;

class OrganizerController extends Controller
{
    public function view(Draw $draw)
    {
        return view('organizer', [
            'draw' => $draw->id,
        ]);
    }

    public function fetch(Draw $draw)
    {
        return response()->json([
            'draw' => $draw->id,
            'participants' => $draw->participants->with('mail')->mapWithKeys(function ($participant) {
                return [$participant->id => $participant->only([
                    'id', 'name', 'address', 'mail',
                ])];
            }),
        ]);
    }

    public function fetchState(Draw $draw)
    {
        return response()->json([
            'participants' => $draw->participants->with('mail')->mapWithKeys(function ($participant) {
                return [$participant->id => $participant->only(['id', 'mail'])];
            }),
        ]);
    }

    public function changeEmail(OrganizerChangeEmailRequest $request, Draw $draw, Participant $participant)
    {
        $participant->address = $request->input('email');
        $participant->save();

        $participant->mail->delivery_status = MailModel::CREATED;
        $participant->mail->save();

        $this->doResendEmail($participant);

        $message = trans('organizer.up_and_sent');

        return $request->ajax() ?
            response()->json([
                'message' => $message, 'participant' => $participant->only(['id', 'mail']),
            ]) :
            redirect('/')->with('message', $message);
    }

    public function resendEmail(OrganizerResendEmailRequest $request, Draw $draw, Participant $participant)
    {
        $participant->mail->delivery_status = MailModel::CREATED;
        $participant->mail->save();

        $this->doResendEmail($participant);

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json(['message' => $message, 'status' => $participant->mail->delivery_status]) :
            redirect('/')->with('message', $message);
    }

    protected function doResendEmail(Participant $participant)
    {
        Metrics::increment('email');

        Mail::to([['email' => $participant->address, 'name' => $participant->name]])
            ->queue(new TargetDrawn($participant));
    }
}
