<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    use HashId;

    protected static $hashConnection = 'bounce';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['delivery_status'];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'delivery_status' => self::CREATED,
    ];

    public const CREATED = 'created';
    public const SENDING = 'sending';
    public const SENT = 'sent';
    public const ERROR = 'error';

    public static $deliveryStatuses = [
        self::CREATED,
        self::SENDING,
        self::SENT,
        self::ERROR,
    ];

    public function updateDeliveryStatus($status)
    {
        $this->delivery_status = $status;
        // Force update, in case the delivery_status did not change
        $this->updated_at = Carbon::now();
        $this->save();
    }
}
