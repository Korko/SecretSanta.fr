<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BounceReason;
use App\Enums\DeliveryStatus;
use App\Events\MailStatusUpdated;
use Carbon\CarbonImmutable;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * @property DeliveryStatus $delivery_status
 * @property string|null $error_code enhanced status code of the bounce (RFC 3463), see BounceReason
 * @property-read BounceReason|null $bounce_reason
 * @property string $notification
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
// What the organizer panel gets of a bounce: the reason, not the code
#[Appends(['bounce_reason'])]
#[Hidden(['error_code'])]
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

    // Whether no email went out in the last $delay seconds, taking the slot if so. A single query: of two
    // simultaneous resends, only one gets true
    public function claimResend(int $delay): bool
    {
        $now = CarbonImmutable::now();
        $claimed = self::query()->whereKey($this->id)
            ->where(fn (Builder $query) => $query->whereNull('updated_at')->orWhere('updated_at', '<=', $now->subSeconds($delay)))
            ->update(['updated_at' => $now]);

        if ($claimed === 0) {
            return false;
        }

        $this->updated_at = $now;
        $this->syncOriginalAttribute('updated_at');

        return true;
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

    public function markAsError(?string $code = null): void
    {
        $this->error_code = $code;
        $this->updateDeliveryStatus(DeliveryStatus::Error);
    }

    // Only news for a mail on its way: a later delay report neither cancels a reception nor an actual bounce
    public function markAsDelayed(): void
    {
        if (! in_array($this->delivery_status, [DeliveryStatus::Received, DeliveryStatus::Error], true)) {
            $this->updateDeliveryStatus(DeliveryStatus::Delayed);
        }
    }

    // Already confirmed (a participant reloading their page, the organizer opening the panel): no write, no broadcast
    public function markAsReceived(): void
    {
        if ($this->delivery_status !== DeliveryStatus::Received) {
            $this->updateDeliveryStatus(DeliveryStatus::Received);
        }
    }

    public function updateDeliveryStatus(DeliveryStatus $status): void
    {
        $this->delivery_status = $status;
        // Sent again, or received after all: the bounce no longer tells anything
        if ($status !== DeliveryStatus::Error) {
            $this->error_code = null;
        }

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

    /** @return Attribute<BounceReason|null, never> */
    protected function bounceReason(): Attribute
    {
        return Attribute::make(get: fn (): ?BounceReason => $this->delivery_status === DeliveryStatus::Error ? BounceReason::fromCode($this->error_code) : null);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['delivery_status' => DeliveryStatus::class];
    }
}
