<?php

namespace App\Http\Controllers;

use Mail;
use Hashids;
use Metrics;
use App\DearSanta;
use App\Http\Requests\DearSantaRequest;
use App\Mail\DearSanta as DearSantaMail;

class DearSantaController extends Controller
{
    public function view(DearSanta $dearSanta)
    {
        return view('dearSanta', [
            'challenge' => $dearSanta->challenge,
            'santa'     => Hashids::encode($dearSanta->id),
        ]);
    }

    public function handle(DearSanta $dearSanta, DearSantaRequest $request)
    {
        $key = base64_decode($request->input('key'));
        $dearSanta->setEncryptionKey($key);

        if ($dearSanta->challenge !== config('app.challenge')) {
            abort(400);
        }

        Metrics::increment('dearsanta');

        $this->sendMail($dearSanta, $request->input('content'));

        $message = trans('message.sent');

        return $request->ajax() ?
            ['message' => $message] :
            redirect('/dearsanta/'.$dearSanta->id)->with('message', $message);
    }

    protected function sendMail(DearSanta $dearSanta, $content)
    {
        Mail::to([['email' => $dearSanta->santa_email, 'name' => $dearSanta->santa_name]])->send(new DearSantaMail($dearSanta->draw, $content));
    }
}
