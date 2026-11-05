<?php

namespace App\Models\Email;

use App\Enums\EmailStatus;
use App\Models\Draw\Draw;
use App\Models\Draw\Organizer;
use App\Models\Draw\Participant;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * EmailDelivery Model - Tracks email delivery status
 */
class EmailDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'draw_id',
        'recipient_type',
        'recipient_id',
        'email_type',
        'email_address',
        'subject',
        'message_id',
        'status',
        'sent_at',
        'delivered_at',
        'bounced_at',
        'bounce_reason',
        'attempts_count',
        'last_attempt_at',
        'metadata',
    ];

    protected $casts = [
        'draw_id' => 'integer',
        'recipient_id' => 'integer',
        'status' => EmailStatus::class,
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'bounced_at' => 'datetime',
        'attempts_count' => 'integer',
        'last_attempt_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($emailDelivery) {
            if (empty($emailDelivery->message_id)) {
                $emailDelivery->message_id = Str::uuid();
            }
            if (empty($emailDelivery->status)) {
                $emailDelivery->status = EmailStatus::PENDING;
            }
        });
    }

    /**
     * Relations
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */
    #[Scope]
    public function pending(Builder $query): Builder
    {
        return $query->where('status', EmailStatus::PENDING);
    }

    #[Scope]
    public function sent(Builder $query): Builder
    {
        return $query->where('status', EmailStatus::SENT);
    }

    #[Scope]
    public function delivered(Builder $query): Builder
    {
        return $query->where('status', EmailStatus::DELIVERED);
    }

    #[Scope]
    public function bounced(Builder $query): Builder
    {
        return $query->where('status', EmailStatus::BOUNCED);
    }

    #[Scope]
    public function forDraw(Builder $query, int $drawId): Builder
    {
        return $query->where('draw_id', $drawId);
    }

    #[Scope]
    public function recentAttempts(Builder $query, int $hours = 24): Builder
    {
        return $query->where('last_attempt_at', '>=', now()->subHours($hours));
    }

    /**
     * State methods
     */
    public function isPending(): bool
    {
        return $this->status === EmailStatus::PENDING;
    }

    public function isSent(): bool
    {
        return $this->status === EmailStatus::SENT;
    }

    public function isDelivered(): bool
    {
        return $this->status === EmailStatus::DELIVERED;
    }

    public function isBounced(): bool
    {
        return $this->status === EmailStatus::BOUNCED;
    }

    public function canRetry(): bool
    {
        return $this->status !== EmailStatus::DELIVERED && $this->attempts_count < 3;
    }

    /**
     * Update status methods
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => EmailStatus::SENT,
            'sent_at' => now(),
            'attempts_count' => $this->attempts_count + 1,
            'last_attempt_at' => now(),
        ]);
    }

    public function markAsDelivered(?string $readConfirmation = null): void
    {
        $metadata = $this->metadata ?? [];
        if ($readConfirmation) {
            $metadata['read_confirmation'] = $readConfirmation;
        }

        $this->update([
            'status' => EmailStatus::DELIVERED,
            'delivered_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    public function markAsBounced(string $reason, ?array $bounceData = null): void
    {
        $metadata = $this->metadata ?? [];
        if ($bounceData) {
            $metadata['bounce_data'] = $bounceData;
        }

        $this->update([
            'status' => EmailStatus::BOUNCED,
            'bounced_at' => now(),
            'bounce_reason' => $reason,
            'metadata' => $metadata,
        ]);
    }

    public function recordAttempt(): void
    {
        $this->update([
            'attempts_count' => $this->attempts_count + 1,
            'last_attempt_at' => now(),
        ]);
    }

    /**
     * Helper methods
     */
    public function getStatusDescription(): string
    {
        return $this->status->getDescription();
    }

    public function getStatusCssClass(): string
    {
        return $this->status->getCssClass();
    }

    public function getTimeSinceLastAttempt(): ?int
    {
        return $this->last_attempt_at?->diffInMinutes(now());
    }

    /**
     * Create email delivery record for a participant
     */
    public static function createForParticipant(
        Draw $draw, 
        Participant $participant, 
        string $emailType, 
        string $subject
    ): self {
        return self::create([
            'draw_id' => $draw->id,
            'recipient_type' => Participant::class,
            'recipient_id' => $participant->id,
            'email_type' => $emailType,
            'email_address' => $participant->email ?? $participant->temp_new_email ?? 'unknown@example.com',
            'subject' => $subject,
        ]);
    }

    /**
     * Create email delivery record for an organizer
     */
    public static function createForOrganizer(
        Draw $draw, 
        Organizer $organizer, 
        string $emailType, 
        string $subject
    ): self {
        return self::create([
            'draw_id' => $draw->id,
            'recipient_type' => Organizer::class,
            'recipient_id' => $organizer->id,
            'email_type' => $emailType,
            'email_address' => $organizer->email ?? 'unknown@example.com',
            'subject' => $subject,
        ]);
    }

    /**
     * Find by message ID (used for processing bounce notifications)
     */
    public static function findByMessageId(string $messageId): ?self
    {
        return self::where('message_id', $messageId)->first();
    }
}