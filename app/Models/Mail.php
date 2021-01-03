<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Mail extends Model
{
    use HashId, DispatchesJobs;

    protected static $hashConnection = 'bounce';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['draw_id'];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'delivery_status' => self::CREATED,
        'version' => 0,
    ];

    public const CREATED = 'created';
    public const SENDING = 'sending';
    public const SENT = 'sent';
    public const ERROR = 'error';
    public const RECEIVED = 'received';

    public static $deliveryStatuses = [
        self::CREATED,
        self::SENDING,
        self::SENT,
        self::ERROR,
        self::RECEIVED,
    ];

    public function updateDeliveryStatus($status)
    {
        $this->delivery_status = $status;

        // Force update, in case the delivery_status did not change
        $this->updated_at = Carbon::now();

        if ($status === self::CREATED) {
            $this->version++;
        }

        $this->save();
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
}
