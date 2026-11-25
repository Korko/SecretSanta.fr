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

    public function markAsCreated(): void
    {
        $this->updateDeliveryStatus(DeliveryStatus::Created);
    }

    // Not over a reception: a retried job whose first attempt got through after all, the recipient having opened their page since
    public function markAsSending(): void
    {
        $this->transition(DeliveryStatus::Sending, except: [DeliveryStatus::Received]);
    }

    // Only the attempt still under way: a reception or a bounce written meanwhile stays
    public function markAsSent(): void
    {
        $this->transition(DeliveryStatus::Sent, only: [DeliveryStatus::Sending]);
    }

    public function markAsError(?string $code = null): void
    {
        $this->error_code = $code;
        $this->updateDeliveryStatus(DeliveryStatus::Error);
    }

    // Only news for a mail on its way: a later delay report neither cancels a reception nor an actual bounce
    public function markAsDelayed(): void
    {
        $this->transition(DeliveryStatus::Delayed, except: [DeliveryStatus::Received, DeliveryStatus::Error]);
    }

    // Already confirmed (a participant reloading their page, the organizer opening the panel): no write, no broadcast
    public function markAsReceived(): void
    {
        $this->transition(DeliveryStatus::Received, except: [DeliveryStatus::Received]);
    }

    public function updateDeliveryStatus(DeliveryStatus $status): void
    {
        $this->delivery_status = $status;
        // Sent again, or received after all: the bounce no longer tells anything
        if ($status !== DeliveryStatus::Error) {
            $this->error_code = null;
        }

        $this->save();

        $this->broadcastStatus();
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

    /**
     * Checked by the database, not on this instance: the worker keeps its own during the whole SMTP session,
     * while the bounce job or the recipient opening their page may have written meanwhile.
     *
     * @param  list<DeliveryStatus>|null  $only  statuses it may move from
     * @param  list<DeliveryStatus>  $except  statuses it may not move from
     */
    private function transition(DeliveryStatus $status, ?array $only = null, array $except = []): void
    {
        $query = self::query()->whereKey($this->getKey());
        if ($only !== null) {
            $query->whereIn('delivery_status', array_map(fn (DeliveryStatus $from): string => $from->value, $only));
        }

        if ($except !== []) {
            $query->whereNotIn('delivery_status', array_map(fn (DeliveryStatus $from): string => $from->value, $except));
        }

        $changed = $query->update(['delivery_status' => $status->value, 'error_code' => null]) > 0;

        // Whether it moved or not, this instance now tells what the database holds
        $fresh = self::query()->whereKey($this->getKey())->first();
        if ($fresh !== null) {
            $this->setRawAttributes($fresh->getAttributes(), true);
        }

        if ($changed) {
            $this->broadcastStatus();
        }
    }

    private function broadcastStatus(): void
    {
        try {
            event(new MailStatusUpdated($this));
        } catch (BroadcastException) {
            // Ignore exception
        }
    }
}
