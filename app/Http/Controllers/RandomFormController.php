<?php

namespace App\Http\Controllers;

use App\Libs\Randomizer;
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
dd($participants, $hat);
        foreach ($hat as $santaIdx => $targetName) {
	    $santa = $participants[$santaIdx];
//            Mail::send('emails.secretsanta', ['name' => $santa['name'], 'secret' => $targetName], function ($m) use ($santa) {
//                $m->to($santa['email'], $santa['name'])->subject("Soirée 'Secret Santa' du dimanche 20 décembre (2ème essai)");
//            });
        }

        return 'Envoyé !';
    }
}
