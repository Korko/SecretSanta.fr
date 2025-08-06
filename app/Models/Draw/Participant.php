<?php

namespace App\Models\Draw;

use App\Casts\EncryptedAttributes;
use App\Models\Message\Message;
use App\Models\Message\MessageReaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model Participant - Participants to thex draws
 */
class Participant extends Model
{
    use HasFactory, EncryptedAttributes;

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
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOrganizers($query)
    {
        return $query->where('is_organizer', true);
    }

    /**
     * Accesseurs for données déchiffrées
     */
    public function getNameAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromContext();
        return $masterKey ? $this->getDecryptedAttribute('name_encrypted', $masterKey) : null;
    }

    public function getEmailAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromContext();
        return $masterKey ? $this->getDecryptedAttribute('email_encrypted', $masterKey) : null;
    }

    /**
     * Méthodes d'état
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to_participant_id);
    }

    /**
     * Actions sur the participant
     */
    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
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
     * Vérifie if ce participant peut être assigné to a autre
     */
    public function canBeAssignedTo(Participant $target): bool
    {
        // Ne peut pas s'assigner to soi-même
        if ($this->id === $target->id) {
            return false;
        }

        // Check les exclusions
        $excludedIds = $this->getExcludedParticipants()->pluck('id');

        return !$excludedIds->contains($target->id);
    }

    /**
     * Récupère the key master depuis the contexte
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implémenter selon the contexte
        return null;
    }
}
