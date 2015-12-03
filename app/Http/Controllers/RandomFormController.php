<?php

namespace Korko\SecretSanta\Http\Controllers;

use Korko\SecretSanta\Http\Requests\RandomFormRequest;
use Korko\SecretSanta\Libs\Randomizer;
use Illuminate\Http\Request;
use Mail;

class RandomFormController extends Controller
{
    public function view()
    {
        return view('randomForm');
    }

    public function handle(RandomFormRequest $request)
    {
        $participants = [];

        for ($i = 0; $i < count($request->input('name')); $i++) {
            $participants[$i] = array(
                'name' => $request->input('name')[$i],
                'email' => $request->input('email')[$i],
                'partner' => array_get($request->input('partner', []), $i)
            );
        }

        $hat = Randomizer::randomize($participants);

        foreach ($hat as $santaIdx => $targetName) {
            $santa = $participants[$santaIdx];

            $content = str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $targetName], $request->input('content'));

            Mail::raw($content, function ($m) use ($santa, $request) {
                $m->to($santa['email'], $santa['name'])->subject($request->input('title'));
            });
        }

        return redirect('/')->with('message', 'Envoyé avec succès !');
    }
}
