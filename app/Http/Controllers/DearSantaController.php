<?php

namespace App\Http\Controllers;

use Mail;
use Hashids;
use Metrics;
use App\Participant;
use App\Mail\DearSanta;
use App\Http\Requests\DearSantaRequest;

class DearSantaController extends Controller
{
    public function view(Participant $participant)
    {
        return view('dearSanta', [
            'challenge' => $participant->draw->challenge,
            'santa'     => Hashids::encode($participant->id),
        ]);
    }

    public function handle(Participant $participant, DearSantaRequest $request)
    {
        if ($participant->draw->challenge !== config('app.challenge')) {
            abort(400);
        }

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
