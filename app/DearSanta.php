<?php

namespace App;

use App\Database\Model;

class DearSanta extends Model
{
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'delivery_status' => self::CREATED,
    ];

    protected $encrypted = [
        'email_body',
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

    public function sender()
    {
        return $this->belongsTo(Participant::class, 'sender_id');
    }
}
