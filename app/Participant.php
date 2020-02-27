<?php

namespace App;

use App\Database\Model;

class Participant extends Model
{
    // Fake property
    public $exclusions;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'delivery_status' => self::CREATED,
    ];

    protected $encrypted = [
        'name',
        'email_address',
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

    public function target()
    {
        return $this->hasOne(Participant::class, 'target_id');
    }

    public function santa()
    {
        return $this->belongsTo(Participant::class, 'target_id');
    }

    public function dearSanta()
    {
        return $this->hasMany(DearSanta::class, 'sender_id');
    }
}
