<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizerChangeEmailRequest;
use App\Http\Requests\OrganizerResendEmailRequest;
use App\Jobs\SendMail;
use App\Mail\TargetDrawn;
use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Metrics;

class OrganizerController extends Controller
{
    public function view(Draw $draw)
    {
        return view('organizer', [
            'draw' => $draw->hash,
        ]);
    }

    public function fetch(Draw $draw)
    {
        return response()->json([
            'draw' => $draw->hash,
            'expires_at' => $draw->expires_at,
            'participants' => $draw->participants->mapWithKeys(function ($participant) {
                return [$participant->id => $participant->only([
                    'id', 'name', 'email', 'mail',
                ])];
            }),
            'changeEmailUrls' => $draw->participants->mapWithKeys(function ($participant) {
                return [$participant->id => URL::signedRoute('organizerPanel.changeEmail', ['participant' => $participant])];
            }),
            'resendEmailUrls' => $draw->participants->mapWithKeys(function ($participant) {
                return [$participant->id => URL::signedRoute('organizerPanel.resendEmail', ['participant' => $participant])];
            }),
        ]);
    }

    public function fetchState(Draw $draw)
    {
        return response()->json([
            'participants' => $draw->participants->mapWithKeys(function ($participant) {
                return [$participant->id => $participant->only(['id', 'mail'])];
            }),
        ]);
    }

    public function changeEmail(OrganizerChangeEmailRequest $request, Draw $draw, Participant $participant)
    {
        $participant->email = $request->input('email');
        $participant->save();

        $participant->mail->updateDeliveryStatus(MailModel::CREATED);

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
        $participant->mail->updateDeliveryStatus(MailModel::CREATED);

        $this->doResendEmail($participant);

        $message = trans('message.sent');

        return $request->ajax() ?
            response()->json(['message' => $message, 'status' => $participant->mail->delivery_status]) :
            redirect('/')->with('message', $message);
    }

    protected function doResendEmail(Participant $participant)
    {
        Metrics::increment('email');

        SendMail::dispatch($participant, new TargetDrawn($participant));
    }

    public function delete(Request $request, Draw $draw)
    {
        $draw->delete();

        $message = trans('organizer.deleted');

        return $request->ajax() ?
            response()->json([
                'message' => $message,
            ]) :
            redirect('/')->with('message', $message);
    }
}
