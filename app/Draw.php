<?php

namespace App;

use App\Services\SymmetricalEncrypter;
use DateInterval;
use DateTime;
use DB;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    public static function prepareAndSave(array $mailContent, $expiration, array $organizer, $encryptionKey, $dearSanta = false)
    {
        $encrypter = new SymmetricalEncrypter($encryptionKey);

        $date = new DateTime('now');
        $date->add(new DateInterval('P7D'));

        // Use symmetrical encryption, we don't need to hide it from each other
        $draw = new self();
        $draw->expiration = $expiration ?: $date->format('Y-m-d H:i:s'); // Default expiration date is 7 days
        $draw->email_title = $encrypter->encrypt($mailContent['title'], false);
        $draw->email_body = $encrypter->encrypt($mailContent['body'], false);
        $draw->organizer_name = $encrypter->encrypt($organizer['name'], false);
        $draw->organizer_email = $encrypter->encrypt($organizer['email'], false);
        $draw->challenge = $encrypter->encrypt(config('app.challenge'), false);
        $draw->dear_santa = $dearSanta;
        $draw->save();

        return $draw;
    }

    public static function cleanup()
    {
        self::where('expiration', '<=', DB::raw('CURRENT_TIMESTAMP'))->delete();
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function dearSanta()
    {
        return $this->hasMany(DearSanta::class);
    }
}
