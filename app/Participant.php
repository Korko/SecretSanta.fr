<?php

namespace App;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => EncryptedString::class,
        'email_address' => EncryptedString::class,
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
