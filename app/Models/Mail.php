<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DeliveryStatus;
use App\Events\MailStatusUpdated;
use Carbon\CarbonImmutable;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * @property DeliveryStatus $delivery_status
 * @property string $notification
 * @property CarbonImmutable|null $updated_at
 */
final class Mail extends Model
{
    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /** @var array<string, mixed> */
    protected $attributes = [
        'delivery_status' => DeliveryStatus::Created,
    ];

    public static function boot(): void
    {
        parent::boot();

        self::creating(function (self $mail): void {
            $mail->notification = Str::uuid()->toString();
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

    // Only news for a mail on its way: a later delay report neither cancels a reception nor an actual bounce
    public function markAsDelayed(): void
    {
        if (! in_array($this->delivery_status, [DeliveryStatus::Received, DeliveryStatus::Error], true)) {
            $this->updateDeliveryStatus(DeliveryStatus::Delayed);
        }
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

    /** @return MorphTo<\Illuminate\Database\Eloquent\Model, $this> dearSanta or participant */
    public function mailable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['delivery_status' => DeliveryStatus::class];
    }
}
