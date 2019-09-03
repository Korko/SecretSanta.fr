<?php

namespace App\Http\Controllers;

use Mail;
use Metrics;
use App\Mail\DearSanta;
use App\Http\Requests\DearSantaRequest;
use App\Services\SymmetricalEncrypter as Encrypter;

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
            [
                'name'  => $encrypter->decrypt($dearSanta->santa_name, false),
                'email' => $encrypter->decrypt($dearSanta->santa_email, false),
            ],
            $request->input('content')
        );

        $message = trans('message.sent');

        return $request->ajax() ?
            ['message' => $message] :
            redirect('/dearsanta/'.$dearSanta->id)->with('message', $message);
    }

    protected function sendMail(Draw $draw, $santa, $content)
    {
        Mail::to([$santa])->send(new DearSantaMail($draw, $content));
    }
}
