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

    public static function prepareAndSave(Draw $draw, array $data, array $target, $encryptionKey)
    {
        $encrypter = new SymmetricalEncrypter($encryptionKey);

        $participant = new self();
        $participant->draw_id = $draw->id;
        $participant->name = $encrypter->encrypt($data['name'], false);
        $participant->email_address = $encrypter->encrypt($data['email'], false);
        $participant->target = $encrypter->encrypt(json_encode($target), false);
        $participant->save();

        return $participant;
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
}
