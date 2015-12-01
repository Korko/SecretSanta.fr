<?php

namespace App\Http\Controllers;

use App\Libs\Randomizer;
use Mail;

class RandomFormController extends Controller
{
    public function view()
    {
        return view('randomForm');
    }

    public function handle()
    {
        $participants = [];

        for ($i = 0; $i < count($_POST['name']); $i++) {
            $participants[$i] = array(
                'name' => $_POST['name'][$i],
                'email' => $_POST['email'][$i],
                'partner' => isset($_POST['partner'][$i]) ? $_POST['partner'][$i] : null
            );
        }

        $hat = Randomizer::randomize($participants);

        foreach ($hat as $santaIdx => $targetName) {
	    $santa = $participants[$santaIdx];
	echo "{$santa['name']} => $targetName<br />";
//            Mail::send('emails.secretsanta', ['name' => $santa['name'], 'secret' => $targetName], function ($m) use ($santa) {
//                $m->to($santa['email'], $santa['name'])->subject("Soirée 'Secret Santa' du dimanche 20 décembre (2ème essai)");
 //           });
        }

        return 'Envoyé !';
    }
}
