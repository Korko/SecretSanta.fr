<?php

namespace App;

use App\Services\SymmetricalEncrypter;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'delivery_status' => self::CREATED,
    ];

    const CREATED = 'created';
    const SENT = 'sent';
    const RECEIVED = 'received';
    const ERROR = 'error';

    public static $deliveryStatuses = [
         self::CREATED,
         self::SENT,
         self::RECEIVED,
         self::ERROR,
    ];

    public static function prepareAndSave(Draw $draw, array $participant, $encryptionKey, $dearSantaLink = null)
    {
        $encrypter = new SymmetricalEncrypter($encryptionKey);

        $participant = new self();
        $participant->draw_id = $draw->id;
        $participant->name = $encrypter->encrypt($participant['name']);
        $participant->email_address = $encrypter->encrypt($participant['email']);
        $participant->dear_santa_link = $dearSantaLink;
        $participant->save();

        return $participant;
    }
}
