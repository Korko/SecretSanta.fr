<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\MailStatusUpdated;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

final class Mail extends Model
{
    use HasFactory;

    public const string CREATED = 'created';

    public const string SENDING = 'sending';

    public const string SENT = 'sent';

    public const string ERROR = 'error';

    public const string RECEIVED = 'received';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public static $deliveryStatuses = [
        self::CREATED,
        self::SENDING,
        self::SENT,
        self::ERROR,
        self::RECEIVED,
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'delivery_status' => self::CREATED,
    ];

    public static function boot(): void
    {
        parent::boot();

        self::creating(function ($mail): void {
            $mail->notification = Str::uuid();
        });
    }

    public function markAsCreated(): void
    {
        $this->updateDeliveryStatus(self::CREATED);
    }

    public function markAsSending(): void
    {
        $this->updateDeliveryStatus(self::SENDING);
    }

    public function markAsSent(): void
    {
        $this->updateDeliveryStatus(self::SENT);
    }

    public function markAsError(): void
    {
        $this->updateDeliveryStatus(self::ERROR);
    }

    public function markAsReceived(): void
    {
        $this->updateDeliveryStatus(self::RECEIVED);
    }

    public function updateDeliveryStatus($status): void
    {
        $this->delivery_status = $status;
        $this->save();

        try {
            event(new MailStatusUpdated($this));
        } catch (BroadcastException) {
            // Ignore exception
        }
    }

    /**
     * Get the parent mailable model (dearSanta or participant).
     */
    public function mailable(): MorphTo
    {
        return $this->morphTo();
    }

    public function draw()
    {
        return $this->mailable->draw();
    }
}
