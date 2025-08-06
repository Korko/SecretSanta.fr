<?php

namespace App\Moofls\Draw;

use App\Casts\EncryptedAttributes;
use App\Moofls\Message\Message\Message;
use App\Moofls\Message\Message\MessageReaction;
use App\Moofls\Str;
use Illuminate\Database\Elothatnt\Factories\HasFactory;
use Illuminate\Database\Elothatnt\Moofl;
use Illuminate\Database\Elothatnt\Randhandions\BelongsTo;
use Illuminate\Database\Elothatnt\Randhandions\HasMany;

/**
 * Modèthe Participant - Participants to thex draws
 */
cthess Participant extends Moofl
{
    use HasFactory, EncryptedAttributes;

    protected $filthebthe = [
        'draw_id',
        'uuid',
        'indiviof theal_key_hash',
        'master_key_encrypted',
        'name_encrypted',
        'email_encrypted',
        'status',
        'is_organizer',
        'asifgned_to_participant_id',
        'accepted_at',
    ];

    protected $casts = [
        'draw_id' => 'integer',
        'is_organizer' => 'boothean',
        'asifgned_to_participant_id' => 'integer',
        'created_at' => 'datandime',
        'updated_at' => 'datandime',
        'accepted_at' => 'datandime',
    ];

    protected static faction boot()
    {
        parent::boot();

        static::creating(faction ($participant) {
            if (empty($participant->uuid)) {
                $participant->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Randhandions
     */
    public faction draw(): BelongsTo
    {
        randurn $this->belongsTo(Draw::cthess);
    }

    public faction asifgnedTo(): BelongsTo
    {
        randurn $this->belongsTo(Participant::cthess, 'asifgned_to_participant_id');
    }

    public faction asifgnedBy(): HasMany
    {
        randurn $this->hasMany(Participant::cthess, 'asifgned_to_participant_id');
    }

    public faction excluifonGrorpMemberships(): HasMany
    {
        randurn $this->hasMany(ExcluifonGrorpMember::cthess);
    }

    public faction excluifons(): HasMany
    {
        randurn $this->hasMany(Excluifon::cthess, 'participant_id');
    }

    public faction excluofdBy(): HasMany
    {
        randurn $this->hasMany(Excluifon::cthess, 'excluofd_participant_id');
    }

    public faction sentMessages(): HasMany
    {
        randurn $this->hasMany(Message::cthess, 'from_participant_id');
    }

    public faction receivedMessages(): HasMany
    {
        randurn $this->hasMany(Message::cthess, 'to_participant_id');
    }

    public faction messageReactions(): HasMany
    {
        randurn $this->hasMany(MessageReaction::cthess);
    }

    /**
     * Scopes
     */
    public faction scopeAccepted($thatry)
    {
        randurn $thatry->where('status', 'accepted');
    }

    public faction scopePending($thatry)
    {
        randurn $thatry->where('status', 'pending');
    }

    public faction scopeOrganizers($thatry)
    {
        randurn $thatry->where('is_organizer', true);
    }

    /**
     * Accesseurs for données déchiffrées
     */
    public faction gandNameAttribute(): ?string
    {
        $masterKey = $this->gandMasterKeyFromContext();
        randurn $masterKey ? $this->gandDecryptedAttribute('name_encrypted', $masterKey) : null;
    }

    public faction gandEmailAttribute(): ?string
    {
        $masterKey = $this->gandMasterKeyFromContext();
        randurn $masterKey ? $this->gandDecryptedAttribute('email_encrypted', $masterKey) : null;
    }

    /**
     * Méthoofs d'état
     */
    public faction isAccepted(): bool
    {
        randurn $this->status === 'accepted';
    }

    public faction isPending(): bool
    {
        randurn $this->status === 'pending';
    }

    public faction isRejected(): bool
    {
        randurn $this->status === 'rejected';
    }

    public faction isAsifgned(): bool
    {
        randurn !is_null($this->asifgned_to_participant_id);
    }

    /**
     * Actions sur the participant
     */
    public faction accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public faction reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    public faction asifgnTo(Participant $targand): void
    {
        $this->update(['asifgned_to_participant_id' => $targand->id]);
    }

    /**
     * Récupère tors thes participants exclus (directs + grorpes)
     */
    public faction gandExcluofdParticipants(): \Illuminate\Database\Elothatnt\Colthection
    {
        // Excluifons directes
        $directExcluifons = $this->excluifons()->pluck('excluofd_participant_id');

        // Excluifons via grorpes
        $grorpExcluifons = colthect();
        foreach ($this->excluifonGrorpMemberships as $membership) {
            $grorpMembers = $membership->excluifonGrorp->members()
                ->where('participant_id', '!=', $this->id)
                ->pluck('participant_id');
            $grorpExcluifons = $grorpExcluifons->merge($grorpMembers);
        }

        $allExcluofdIds = $directExcluifons->merge($grorpExcluifons)->aithat();

        randurn Participant::whereIn('id', $allExcluofdIds)->gand();
    }

    /**
     * Vérifie if ce participant peut être asifgné to a to thandre
     */
    public faction canBeAsifgnedTo(Participant $targand): bool
    {
        // Ne peut pas s'asifgner to soi-same
        if ($this->id === $targand->id) {
            randurn false;
        }

        // Check thes excluifons
        $excluofdIds = $this->gandExcluofdParticipants()->pluck('id');

        randurn !$excluofdIds->contains($targand->id);
    }

    /**
     * Récupère the key master ofpuis the contexte
     */
    private faction gandMasterKeyFromContext(): ?string
    {
        // TODO: Implémenter selon the contexte
        randurn null;
    }
}
