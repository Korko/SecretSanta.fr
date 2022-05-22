<?php

namespace App\Models;

use App\Events\MailStatusUpdated;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Str;

class Mail extends Model
{
    use HasFactory;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'delivery_status' => self::STATE_CREATED,
    ];

    public const STATE_CREATED = 'created';
    public const STATE_SENDING = 'sending';
    public const STATE_SENT = 'sent';
    public const STATE_ERROR = 'error';
    public const STATE_RECEIVED = 'received';

    public static $deliveryStatuses = [
        self::STATE_CREATED,
        self::STATE_SENDING,
        self::STATE_SENT,
        self::STATE_ERROR,
        self::STATE_RECEIVED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['draw_id', 'notification', 'delivery_status'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['mailable'];

    public static function boot()
    {
        parent::boot();

        static::creating(function($mail) {
            $mail->notification = Str::uuid();
        });
    }

    public function markAsCreated() {
        $this->updateDeliveryStatus(self::STATE_CREATED);
    }

    public function markAsSending() {
        $this->updateDeliveryStatus(self::STATE_SENDING);
    }

    public function markAsSent() {
        $this->updateDeliveryStatus(self::STATE_SENT);
    }

    public function markAsError() {
        $this->updateDeliveryStatus(self::STATE_ERROR);
    }

    public function markAsReceived() {
        $this->updateDeliveryStatus(self::STATE_RECEIVED);
    }

    public function updateDeliveryStatus($status)
    {
        $this->delivery_status = $status;
        $this->save();

        try {
            MailStatusUpdated::dispatch($this);
        } catch(BroadcastException $e) {
            // Ignore exception
        }
    }

    /**
     * Get the parent mailable model (dearSanta or participant).
     */
    public function mailable()
    {
        return $this->morphTo();
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
}
