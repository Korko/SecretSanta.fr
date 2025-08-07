<?php

namespace App\Models\Draw;

use App\Casts\EncryptedAttributes;
use App\Enums\ParticipantStatus;
use App\Models\Message\Message;
use App\Models\Message\MessageReaction;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model Participant - Participants in draws
 */
class Participant extends Model
{
    use HasFactory, EncryptedAttributes;

    // Property Hooks with Enum validation
    public ParticipantStatus $status {
        set(ParticipantStatus|string $value) {
            $enumValue = $value instanceof ParticipantStatus ? $value : ParticipantStatus::from($value);
            $this->attributes['status'] = $enumValue->value;
        }

        get => ParticipantStatus::from($this->attributes['status'] ?? 'pending');
    }


    protected $fillable = [
        'draw_id',
        'uuid',
        'individual_key_hash',
        'master_key_encrypted',
        'name_encrypted',
        'email_encrypted',
        'status',
        'is_organizer',
        'assigned_to_participant_id',
        'accepted_at',
    ];

    protected $casts = [
        'draw_id' => 'integer',
        'status' => ParticipantStatus::class,
        'is_organizer' => 'boolean',
        'assigned_to_participant_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($participant) {
            if (empty($participant->uuid)) {
                $participant->uuid = (string) Str::uuid();
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

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'assigned_to_participant_id');
    }

    public function assignedBy(): HasMany
    {
        return $this->hasMany(Participant::class, 'assigned_to_participant_id');
    }

    public function exclusionGroupMemberships(): HasMany
    {
        return $this->hasMany(ExclusionGroupMember::class);
    }

    public function exclusions(): HasMany
    {
        return $this->hasMany(Exclusion::class, 'participant_id');
    }

    public function excludedBy(): HasMany
    {
        return $this->hasMany(Exclusion::class, 'excluded_participant_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'from_participant_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'to_participant_id');
    }

    public function messageReactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }

    /**
     * Scopes
     */
    #[Scope]
    public function accepted(Builder $query)
    {
        return $query->where('status', ParticipantStatus::ACCEPTED);
    }

    #[Scope]
    public function pending(Builder $query)
    {
        return $query->where('status', ParticipantStatus::PENDING);
    }

    #[Scope]
    public function active(Builder $query)
    {
        return $query->whereIn('status', [
            ...array_filter(ParticipantStatus::cases(), fn($status) => $status->isActive())
        ]);
    }

    #[Scope]
    public function organizers(Builder $query)
    {
        return $query->where('is_organizer', true);
    }

    /**
     * Accessors for decrypted content
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                $masterKey = $this->getMasterKeyFromContext();
                return $masterKey ? $this->getDecryptedAttribute('name_encrypted', $masterKey) : null;
            }
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: function () {
                $masterKey = $this->getMasterKeyFromContext();
                return $masterKey ? $this->getDecryptedAttribute('email_encrypted', $masterKey) : null;
            }
        );
    }

    /**
     * State methods
     */
    public function isAccepted(): bool
    {
        return $this->status === ParticipantStatus::ACCEPTED;
    }

    public function isPending(): bool
    {
        return $this->status === ParticipantStatus::PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === ParticipantStatus::REJECTED;
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to_participant_id);
    }

    /**
     * Actions with transition validation
     */
    public function accept(): void
    {
        if (!$this->status->canTransitionTo(ParticipantStatus::ACCEPTED)) {
            throw new \InvalidArgumentException('Cannot accept participant from current status');
        }
        $this->status = ParticipantStatus::ACCEPTED;
        $this->accepted_at = now();
        $this->save();
    }

    public function reject(): void
    {
        if (!$this->status->canTransitionTo(ParticipantStatus::REJECTED)) {
            throw new \InvalidArgumentException('Cannot reject participant from current status');
        }
        $this->status = ParticipantStatus::REJECTED;
        $this->save();
    }

    public function assignTo(Participant $target): void
    {
        $this->update(['assigned_to_participant_id' => $target->id]);
    }

    /**
     * Récupère tous les participants exclus (directs + groupes)
     */
    public function getExcludedParticipants(): \Illuminate\Database\Eloquent\Collection
    {
        // Exclusions directes
        $directExclusions = $this->exclusions()->pluck('excluded_participant_id');

        // Exclusions via groupes
        $groupExclusions = collect();
        foreach ($this->exclusionGroupMemberships as $membership) {
            $groupMembers = $membership->exclusionGroup->members()
                ->where('participant_id', '!=', $this->id)
                ->pluck('participant_id');
            $groupExclusions = $groupExclusions->merge($groupMembers);
        }

        $allExcludedIds = $directExclusions->merge($groupExclusions)->unique();

        return Participant::whereIn('id', $allExcludedIds)->get();
    }

    /**
     * Verifies if this participant can be assigned to another
     */
    public function canBeAssignedTo(Participant $target): bool
    {
        // Cannot assign to self
        if ($this->id === $target->id) {
            return false;
        }

        // Check exclusions
        $excludedIds = $this->getExcludedParticipants()->pluck('id');

        return !$excludedIds->contains($target->id);
    }

    /**
     * Retrieve the master key from context
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implement according to context
        return null;
    }
}
