<?php

namespace App;

use App\Services\SymmetricalEncrypter;
use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    public static function prepareAndSave(array $mailContent, $expiration, array $organizer, $encryptionKey, $dearSantaDraw = null)
    {
        $encrypter = new SymmetricalEncrypter($encryptionKey);

        $date = new DateTime('now');
        $date->add(new DateInterval('P7D'));

        // Use symmetrical encryption, we don't need to hide it from each other
        $draw = new self();
        $draw->expiration = $expiration ?: $date->format('Y-m-d H:i:s'); // Default expiration date is 7 days
        $draw->email_title = $encrypter->encrypt($mailContent['title']);
        $draw->email_body = $encrypter->encrypt($mailContent['body']);
        $draw->organizer_name = $encrypter->encrypt($organizer['name']);
        $draw->organizer_email = $encrypter->encrypt($organizer['email']);
        $draw->dear_santa_draw_id = $dearSantaDraw ? $dearSantaDraw->id : null;
        $draw->save();

        return $draw;
    }
}
