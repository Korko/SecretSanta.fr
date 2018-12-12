<?php

namespace App;

use App\Services\SymmetricalEncrypter;
use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class DearSantaDraw extends Model
{
    public static function prepareAndSave(array $mailContent, $expiration, array $organizer, $encryptionKey)
    {
        $encrypter = new SymmetricalEncrypter($encryptionKey);

        $date = new DateTime('now');
        $date->add(new DateInterval('P7D'));

        // Use symmetrical encryption, we don't need to hide it from each other
        $draw = new self();
        $draw->expiration = $expiration ?: $date->format('Y-m-d H:i:s'); // Default expiration date is 7 days
        $draw->title = $encrypter->encrypt($mailContent['title']);
        $draw->body = $encrypter->encrypt($mailContent['body']);
        $draw->organizer_name = $encrypter->encrypt($organizer['name']);
        $draw->organizer_email = $encrypter->encrypt($organizer['email']);
        $draw->save();

        return $draw;
    }
}
