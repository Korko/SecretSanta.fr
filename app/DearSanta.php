<?php

namespace App;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_body' => EncryptedString::class,
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
