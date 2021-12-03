<?php

namespace App\Models;

use App\Events\MailStatusUpdated;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'delivery_status' => self::CREATED,
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

    public function markAsCreated() {
        $this->updateDeliveryStatus(self::CREATED);
    }

    public function markAsSending() {
        $this->updateDeliveryStatus(self::SENDING);
    }

    public function markAsSent() {
        $this->updateDeliveryStatus(self::SENT);
    }

    public function markAsError() {
        $this->updateDeliveryStatus(self::ERROR);
    }

    public function markAsReceived() {
        $this->updateDeliveryStatus(self::RECEIVED);
    }

    public function updateDeliveryStatus($status)
    {
        $this->delivery_status = $status;
        $this->save();

        try {
            MailStatusUpdated::dispatch($this);
        } catch(Exception $e) {
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
        return $this->mailable->draw();
    }
}
