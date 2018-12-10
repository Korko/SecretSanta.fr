<?php

namespace App;

use App\Services\AsymmetricalPublicEncrypter as AsymmetricalEncrypter;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    const CHALLENGE = 'Ping?';

    public $timestamps = false;

    public static function prepareAndSave(Draw $draw, array $santa, $publicEncryptionKey)
    {
        $encrypter = new AsymmetricalEncrypter($publicEncryptionKey);

        // Use Asymmetrical encrypter, only the reciptient should be able to decrypt!
        $participant = new self();
        $participant->draw_id = $draw->id;
        $participant->santa_name = $encrypter->encrypt($santa['name']);
        $participant->santa_email = $encrypter->encrypt($santa['email']);
        $participant->challenge = $encrypter->encrypt(Participant::CHALLENGE, false); // tested by JS so no serializing
        $participant->public_key = $publicEncryptionKey;

        $participant->save();

        return $participant;
    }
}
