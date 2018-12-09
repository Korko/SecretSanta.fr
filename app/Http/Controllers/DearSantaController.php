<?php

namespace App\Http\Controllers;

use App\Http\Requests\DearSantaRequest;
use App\Mail\DearSanta;
use App\Participant;
use App\Services\AsymmetricalEncrypter as Encrypter;
use Illuminate\Http\Request;
use Mail;
use Metrics;

class DearSantaController extends Controller
{
    public function view(Participant $participant)
    {
        list('iv' => $iv, 'value' => $challenge) = Encrypter::split($participant->challenge);

        return view('dearSanta', [
            'challenge' => $challenge,
            'iv'        => $iv,
            'santa'     => $participant->id,
        ]);
    }

    public function handle(Participant $participant, DearSantaRequest $request)
    {
        $santa = $this->getSanta($participant, $request);

        Metrics::increment('dearsanta');

        $this->sendMail($santa, $request->input('title'), $request->input('content'));

        $message = trans('message.sent');

        return $request->ajax() ?
            ['message' => $message] :
            redirect('/dearsanta/'.$participant->id)->with('message', $message);
    }

    private function getSanta(Participant $participant, Request $request)
    {
        $key = hex2bin($request->input('key'));

        $encrypter = new Encrypter($key);

        return [
            'name'  => $encrypter->decrypt($participant->santa_name),
            'email' => $encrypter->decrypt($participant->santa_email),
        ];
    }

    protected function sendMail(array $santa, $title, $content)
    {
        Mail::to($santa['email'], $santa['name'])->send(new DearSanta($title, $content));
    }
}
