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
