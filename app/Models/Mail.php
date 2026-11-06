<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DeliveryStatus;
use App\Events\MailStatusUpdated;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * @property DeliveryStatus $delivery_status
 */
final class Mail extends Model
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
        'delivery_status' => DeliveryStatus::Created,
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
        $this->updateDeliveryStatus(DeliveryStatus::Created);
    }

    public function markAsSending(): void
    {
        $this->updateDeliveryStatus(DeliveryStatus::Sending);
    }

    public function markAsSent(): void
    {
        $this->updateDeliveryStatus(DeliveryStatus::Sent);
    }

    public function markAsError(): void
    {
        $this->updateDeliveryStatus(DeliveryStatus::Error);
    }

    public function markAsReceived(): void
    {
        $this->updateDeliveryStatus(DeliveryStatus::Received);
    }

    public function updateDeliveryStatus(DeliveryStatus $status): void
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
    /** @return MorphTo<\Illuminate\Database\Eloquent\Model, $this> */
    public function mailable(): MorphTo
    {
        return $this->morphTo();
    }

    public function draw()
    {
        return $this->mailable->draw();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['delivery_status' => DeliveryStatus::class];
    }
}
