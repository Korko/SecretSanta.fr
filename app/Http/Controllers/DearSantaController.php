<?php

namespace App\Http\Controllers;

use App\Http\Requests\DearSantaRequest;
use App\Mail\DearSanta as DearSantaMail;
use App\DearSanta;
use App\Services\AsymmetricalPrivateEncrypter as Encrypter;
use Illuminate\Http\Request;
use Mail;
use Metrics;

class DearSantaController extends Controller
{
    public function view(DearSanta $dearSanta)
    {
        return view('dearSanta', [
            'challenge' => $dearSanta->challenge,
            'santa'     => $dearSanta->id,
        ]);
    }

    public function handle(DearSanta $dearSanta, DearSantaRequest $request)
    {
        $santa = $this->getSanta($dearSanta, $request);

        Metrics::increment('dearsanta');

        $this->sendMail($santa, $request->input('title'), $request->input('content'));

        $message = trans('message.sent');

        return $request->ajax() ?
            ['message' => $message] :
            redirect('/dearsanta/'.$dearSanta->id)->with('message', $message);
    }

    private function getSanta(DearSanta $dearSanta, Request $request)
    {
        $key = base64_decode($request->input('key'));

        $encrypter = new Encrypter($key);

        return [
            'name'  => $encrypter->decrypt($dearSanta->santa_name),
            'email' => $encrypter->decrypt($dearSanta->santa_email),
        ];
    }

    protected function sendMail(array $santa, $title, $content)
    {
        Mail::to($santa['email'], $santa['name'])->send(new DearSantaMail($title, $content));
    }
}
