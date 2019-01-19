<?php

namespace App;

use App\Services\SymmetricalEncrypter;
use Illuminate\Database\Eloquent\Model;

class DearSanta extends Model
{
    public $timestamps = false;

    public static function prepareAndSave(Draw $draw, array $santa, $encryptionKey)
    {
        $encrypter = new SymmetricalEncrypter($encryptionKey);

        $dearSanta = new self();
        $dearSanta->draw_id = $draw->id;
        $dearSanta->santa_name = $encrypter->encrypt($santa['name']);
        $dearSanta->santa_email = $encrypter->encrypt($santa['email']);
        $dearSanta->challenge = $encrypter->encrypt(config('app.challenge'), false); // tested by JS so no serializing

        $dearSanta->save();

        return $dearSanta;
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
}
