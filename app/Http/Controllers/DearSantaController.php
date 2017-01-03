<?php

namespace Korko\SecretSanta\Http\Controllers;

use Korko\SecretSanta\Participant;

class DearSantaController extends Controller
{
    public function view(Participant $santa)
    {
        // Times 2 because we are in UTF8 and the iv is in hexadecimal
        $ivLength = openssl_cipher_iv_length('aes256') * 2;

        return view('dearSanta', [
            'challenge' => substr($santa->challenge, $ivLength),
            'iv'        => substr($santa->challenge, 0, $ivLength),
            'santa'     => $santa->id,
        ]);
    }
}
