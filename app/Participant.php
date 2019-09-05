<?php

namespace App;

use App\Database\Model;

class Participant extends Model
{
    // Fake attributes
    public $phone_number;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'delivery_status' => self::CREATED,
    ];

    protected $casts = [
        'target' => 'object',
    ];

    protected $encryptable = [
        'name',
        'email_address',
        'target',
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

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
}
