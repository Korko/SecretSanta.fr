<?php

namespace App\Models\Message;

use App\Casts\EncryptedAttributes;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Message - Messages between participants
 */
class Message extends Model
{
    use HasFactory, EncryptedAttributes;

    protected $fillable = [
        'draw_id',
        'from_participant_id',
        'to_participant_id',
        'encryption_version',
        'content_encrypted',
        'content_hash',
        'type',
        'status',
        'moderation_status',
        'moderation_data',
        'read_at',
    ];

    protected $casts = [
        'draw_id' => 'integer',
        'from_participant_id' => 'integer',
        'to_participant_id' => 'integer',
        'encryption_version' => 'integer',
        'moderation_data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    public function fromParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'from_participant_id');
    }

    public function toParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'to_participant_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }

    /**
     * Scopes
     */
    #[Scope]
    public function toSecretSanta(Builder $query)
    {
        return $query->where('type', 'to_secret_santa');
    }

    #[Scope]
    public function toTarget(Builder $query)
    {
        return $query->where('type', 'to_target');
    }

    #[Scope]
    public function reported(Builder $query)
    {
        return $query->where('moderation_status', 'reported');
    }

    #[Scope]
    public function unreviewed(Builder $query)
    {
        return $query->where('moderation_status', 'reported');
    }

    #[Scope]
    public function forParticipant(Builder $query, int $participantId)
    {
        return $query->where(function ($q) use ($participantId) {
            $q->where('from_participant_id', $participantId)
                ->orWhere('to_participant_id', $participantId);
        });
    }

    /**
     * Accessor and Mutator for decrypted content
     */
    protected function content(): Attribute
    {
        return Attribute::make(
            get: function () {
                $masterKey = $this->getMasterKeyFromContext();
                return $masterKey ? $this->getDecryptedAttribute('content_encrypted', $masterKey) : null;
            },
            set: function (string $value) {
                $masterKey = $this->getMasterKeyFromContext();
                if ($masterKey) {
                    $this->setEncryptedAttribute('content_encrypted', $value, $masterKey);
                }
                return $value;
            }
        );
    }

    /**
     * State methods
     */
    public function isToSecretSanta(): bool
    {
        return $this->type === 'to_secret_santa';
    }

    public function isToTarget(): bool
    {
        return $this->type === 'to_target';
    }

    public function isReported(): bool
    {
        return $this->moderation_status === 'reported';
    }

    public function isReviewed(): bool
    {
        return $this->moderation_status === 'reviewing';
    }

    /**
     * Actions on the message
     */
    public function report(): void
    {
        $this->update(['moderation_status' => 'reported']);
    }

    public function markAsReviewed(?string $notes = null): void
    {
        $moderationData = $this->moderation_data ?? [];
        if ($notes) {
            $moderationData['reviewer_notes'] = $notes;
        }
        
        $this->update([
            'moderation_status' => 'reviewing',
            'moderation_data' => $moderationData,
        ]);
    }

    /**
     * Add a reaction to the message
     */
    public function addReaction(Participant $participant, string $reaction): MessageReaction
    {
        return $this->reactions()->updateOrCreate(
            ['participant_id' => $participant->id],
            ['reaction' => $reaction]
        );
    }

    /**
     * Remove a reaction from the message
     */
    public function removeReaction(Participant $participant): bool
    {
        return $this->reactions()
                ->where('participant_id', $participant->id)
                ->delete() > 0;
    }

    /**
     * Check if a participant can see this message
     */
    public function canBeSeenBy(Participant $participant): bool
    {
        return $this->from_participant_id === $participant->id
            || $this->to_participant_id === $participant->id;
    }

    /**
     * Check if a participant can reply to this message
     */
    public function canBeRepliedBy(Participant $participant): bool
    {
        // Only the recipient can reply
        if ($this->to_participant_id !== $participant->id) {
            return false;
        }

        // Check the draw settings
        if (!$this->draw->allow_target_messages) {
            return false;
        }

        // If it's a message to the secret santa, the target can reply
        if ($this->isToSecretSanta()) {
            return true;
        }

        return false;
    }

    /**
     * Create a reply to this message
     */
    public function createReply(string $content): self
    {
        return self::create([
            'draw_id' => $this->draw_id,
            'from_participant_id' => $this->to_participant_id,
            'to_participant_id' => $this->from_participant_id,
            'content' => $content,
            'type' => 'to_target', // The reply always goes to the target
        ]);
    }

    /**
     * Get the conversation between two participants
     */
    public static function getConversation(int $participant1Id, int $participant2Id): \Illuminate\Database\Eloquent\Collection
    {
        return self::where(function ($query) use ($participant1Id, $participant2Id) {
            $query->where('from_participant_id', $participant1Id)
                ->where('to_participant_id', $participant2Id);
        })->orWhere(function ($query) use ($participant1Id, $participant2Id) {
            $query->where('from_participant_id', $participant2Id)
                ->where('to_participant_id', $participant1Id);
        })->orderBy('created_at')->get();
    }

    /**
     * Get the master key from context
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implement according to context
        return null;
    }
}
