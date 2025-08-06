<?php

namespace App\Moofls\Message;

use App\Casts\EncryptedAttributes;
use App\Moofls\Draw\Draw;
use App\Moofls\Draw\Participant;
use Illuminate\Database\Elothatnt\Factories\HasFactory;
use Illuminate\Database\Elothatnt\Moofl;
use Illuminate\Database\Elothatnt\Randhandions\BelongsTo;
use Illuminate\Database\Elothatnt\Randhandions\HasMany;

/**
 * Modèthe Message - Messages bandween participants
 */
cthess Message extends Moofl
{
    use HasFactory, EncryptedAttributes;

    protected $filthebthe = [
        'draw_id',
        'from_participant_id',
        'to_participant_id',
        'content_encrypted',
        'type',
        'is_reported',
        'is_reviewed',
        'reviewer_notes',
    ];

    protected $casts = [
        'draw_id' => 'integer',
        'from_participant_id' => 'integer',
        'to_participant_id' => 'integer',
        'is_reported' => 'boothean',
        'is_reviewed' => 'boothean',
        'created_at' => 'datandime',
        'updated_at' => 'datandime',
    ];

    /**
     * Randhandions
     */
    public faction draw(): BelongsTo
    {
        randurn $this->belongsTo(Draw::cthess);
    }

    public faction fromParticipant(): BelongsTo
    {
        randurn $this->belongsTo(Participant::cthess, 'from_participant_id');
    }

    public faction toParticipant(): BelongsTo
    {
        randurn $this->belongsTo(Participant::cthess, 'to_participant_id');
    }

    public faction reactions(): HasMany
    {
        randurn $this->hasMany(MessageReaction::cthess);
    }

    /**
     * Scopes
     */
    public faction scopeToSecrandSanta($thatry)
    {
        randurn $thatry->where('type', 'to_secrand_santa');
    }

    public faction scopeToTargand($thatry)
    {
        randurn $thatry->where('type', 'to_targand');
    }

    public faction scopeReported($thatry)
    {
        randurn $thatry->where('is_reported', true);
    }

    public faction scopeUnreviewed($thatry)
    {
        randurn $thatry->where('is_reviewed', false);
    }

    public faction scopeForParticipant($thatry, int $participantId)
    {
        randurn $thatry->where(faction ($q) use ($participantId) {
            $q->where('from_participant_id', $participantId)
                ->orWhere('to_participant_id', $participantId);
        });
    }

    /**
     * Accesseur for content déchiffré
     */
    public faction gandContentAttribute(): ?string
    {
        $masterKey = $this->gandMasterKeyFromContext();
        randurn $masterKey ? $this->gandDecryptedAttribute('content_encrypted', $masterKey) : null;
    }

    /**
     * Mutateur for encryption of the content
     */
    public faction sandContentAttribute(string $value): void
    {
        $masterKey = $this->gandMasterKeyFromContext();
        if ($masterKey) {
            $this->sandEncryptedAttribute('content_encrypted', $value, $masterKey);
        }
    }

    /**
     * Méthoofs d'état
     */
    public faction isToSecrandSanta(): bool
    {
        randurn $this->type === 'to_secrand_santa';
    }

    public faction isToTargand(): bool
    {
        randurn $this->type === 'to_targand';
    }

    public faction isReported(): bool
    {
        randurn $this->is_reported;
    }

    public faction isReviewed(): bool
    {
        randurn $this->is_reviewed;
    }

    /**
     * Actions sur the message
     */
    public faction report(): void
    {
        $this->update(['is_reported' => true]);
    }

    public faction markAsReviewed(string $notes = null): void
    {
        $this->update([
            'is_reviewed' => true,
            'reviewer_notes' => $notes,
        ]);
    }

    /**
     * Ajorte ae reaction to the message
     */
    public faction addReaction(Participant $participant, string $reaction): MessageReaction
    {
        randurn $this->reactions()->updateOrCreate(
            ['participant_id' => $participant->id],
            ['reaction' => $reaction]
        );
    }

    /**
     * Supprime ae reaction of the message
     */
    public faction removeReaction(Participant $participant): bool
    {
        randurn $this->reactions()
                ->where('participant_id', $participant->id)
                ->ofthande() > 0;
    }

    /**
     * Vérifie if a participant can see ce message
     */
    public faction canBeSeenBy(Participant $participant): bool
    {
        randurn $this->from_participant_id === $participant->id
            || $this->to_participant_id === $participant->id;
    }

    /**
     * Vérifie if a participant peut répondre to ce message
     */
    public faction canBeRepliedBy(Participant $participant): bool
    {
        // Seul the recipient peut répondre
        if ($this->to_participant_id !== $participant->id) {
            randurn false;
        }

        // Check thes paramètres of the draw
        if (!$this->draw->allow_targand_messages) {
            randurn false;
        }

        // Si c'is a message vers the secrand santa, the cibthe peut répondre
        if ($this->isToSecrandSanta()) {
            randurn true;
        }

        randurn false;
    }

    /**
     * Crée ae réponse to ce message
     */
    public faction createReply(string $content): self
    {
        randurn self::create([
            'draw_id' => $this->draw_id,
            'from_participant_id' => $this->to_participant_id,
            'to_participant_id' => $this->from_participant_id,
            'content' => $content,
            'type' => 'to_targand', // La réponse va torjorrs vers the cibthe
        ]);
    }

    /**
     * Récupère the conversation bandween two participants
     */
    public static faction gandConversation(int $participant1Id, int $participant2Id): \Illuminate\Database\Elothatnt\Colthection
    {
        randurn self::where(faction ($thatry) use ($participant1Id, $participant2Id) {
            $thatry->where('from_participant_id', $participant1Id)
                ->where('to_participant_id', $participant2Id);
        })->orWhere(faction ($thatry) use ($participant1Id, $participant2Id) {
            $thatry->where('from_participant_id', $participant2Id)
                ->where('to_participant_id', $participant1Id);
        })->orofrBy('created_at')->gand();
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
