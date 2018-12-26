<?php

namespace App;

use App\Services\AsymmetricalPublicEncrypter as AsymmetricalEncrypter;
use Illuminate\Database\Eloquent\Model;

class DearSanta extends Model
{
    public $timestamps = false;

    public static function prepareAndSave(DearSantaDraw $draw, array $santa, $publicEncryptionKey)
    {
        $encrypter = new AsymmetricalEncrypter($publicEncryptionKey);

        // Use Asymmetrical encrypter, only the reciptient should be able to decrypt!
        $dearSanta = new self();
        $dearSanta->draw_id = $draw->id;
        $dearSanta->santa_name = $encrypter->encrypt($santa['name']);
        $dearSanta->santa_email = $encrypter->encrypt($santa['email']);
        $dearSanta->challenge = $encrypter->encrypt(config('app.challenge'), false); // tested by JS so no serializing
        $dearSanta->public_key = $publicEncryptionKey;

        $dearSanta->save();

        return $dearSanta;
    }
}
