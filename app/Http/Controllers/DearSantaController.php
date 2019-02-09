<?php

namespace App\Http\Controllers;

use App\DearSanta;
use App\Draw;
use App\Http\Requests\DearSantaRequest;
use App\Mail\DearSanta as DearSantaMail;
use App\Services\SymmetricalEncrypter as Encrypter;
use Hashids;
use Mail;
use Metrics;

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
        $encrypter = new Encrypter($key);

        if ($encrypter->decrypt($dearSanta->challenge, false) !== config('app.challenge')) {
            abort(400);
        }

        Metrics::increment('dearsanta');

        $this->sendMail(
            $dearSanta->draw,
            $encrypter->decrypt($dearSanta->santa_name),
            $encrypter->decrypt($dearSanta->santa_email),
            $request->input('content')
        );

        $message = trans('message.sent');

        return $request->ajax() ?
            ['message' => $message] :
            redirect('/dearsanta/'.$dearSanta->id)->with('message', $message);
    }

    protected function sendMail(Draw $draw, $santaName, $santaEmail, $content)
    {
        Mail::to($santaEmail, $santaName)->send(new DearSantaMail($draw, $content));
    }
}
