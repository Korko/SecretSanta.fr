<?php

namespace Korko\SecretSanta\Http\Controllers;

use Illuminate\Http\Request;
use Korko\SecretSanta\Http\Requests\DearSantaRequest;
use Korko\SecretSanta\Mail\DearSanta;
use Korko\SecretSanta\Participant;
use Mail;
use Metrics;

class DearSantaController extends Controller
{
    public function view(Participant $participant)
    {
        // Times 2 because we are in UTF8 and the iv is in hexadecimal
        $ivLength = openssl_cipher_iv_length('aes256') * 2;

        return view('dearSanta', [
            'challenge' => substr($participant->challenge, $ivLength),
            'iv'        => substr($participant->challenge, 0, $ivLength),
            'santa'     => $participant->id,
        ]);
    }

    public function handle(Participant $participant, DearSantaRequest $request)
    {
        $santa = $this->getSanta($participant, $request);

        Metrics::increment('dearsanta');

        $this->sendMail($santa, $request->input('title'), $request->input('content'));

        $message = trans('message.sent');

        return $request->ajax() ? ['message' => $message] : redirect('/dearsanta/'.$participant->id)->with('message', $message);
    }

    private function getSanta(Participant $participant, Request $request)
    {
        $key = hex2bin($request->input('key'));

        // Times 2 because we are in UTF8 and the iv is in hexadecimal
        $ivLength = openssl_cipher_iv_length('aes256') * 2;

        $santa = substr($participant->santa, $ivLength);
        $iv = hex2bin(substr($participant->santa, 0, $ivLength));

        $santaR = openssl_decrypt($santa, 'aes256', $key, 0, $iv);
        $santa = unserialize($santaR);

        return $santa;
    }

    protected function sendMail(array $santa, $title, $content)
    {
        Mail::to($santa['email'], $santa['name'])->send(new DearSanta($title, $content));
    }
}
