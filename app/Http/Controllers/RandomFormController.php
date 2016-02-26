<?php

namespace Korko\SecretSanta\Http\Controllers;

use Korko\SecretSanta\Http\Requests\RandomFormRequest;
use Korko\SecretSanta\Libs\Randomizer;
use Mail;
use Statsd;

class RandomFormController extends Controller
{
    public function view()
    {
        return view('randomForm');
    }

    public function handle(RandomFormRequest $request)
    {
        $names = $request->input('name');
        $emails = $request->input('email');
        $partners = $request->input('partner', []);

        $participants = [];
        for ($i = 0; $i < count($names); $i++) {
            $participants[$i] = [
                'name'    => $names[$i],
                'email'   => $emails[$i],
                'partner' => !empty($partners[$i]) ? $names[$partners[$i]] : null,
            ];
        }

        Statsd::gauge('draws', '+1');
        Statsd::gauge('participants', '+'.count($participants));

        $hat = Randomizer::randomize($participants);

        foreach ($hat as $santaIdx => $targetName) {
            $santa = $participants[$santaIdx];

            $content = str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $targetName], $request->input('content'));

            Mail::raw($content, function ($m) use ($santa, $request) {
                $m->to($santa['email'], $santa['name'])->subject($request->input('title'));
            });
        }

        $message = 'Envoyé avec succès !';

        return $request->ajax() ? [$message] : redirect('/')->with('message', $message);
    }
}
