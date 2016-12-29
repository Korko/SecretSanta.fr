<?php

namespace Korko\SecretSanta\Http\Controllers;

use Illuminate\Http\Request;
use Korko\SecretSanta\Participant;

class DearSantaController extends Controller
{
    public function view(Participant $santa)
    {
        return view('dearSanta', [
            'challenge' => $santa->challenge,
            'iv' => bin2hex($santa->iv)
        ]);
    }
}
