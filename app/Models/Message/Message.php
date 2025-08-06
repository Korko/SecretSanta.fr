<?php

namespace App\Models\Message;

use App\Casts\EncryptedAttributes;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Message - Messages entre participants
 */
class Message extends Model
{
    use HasFactory, EncryptedAttributes;

    protected $fillable = [
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
        'is_reported' => 'boolean',
        'is_reviewed' => 'boolean',
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
    public function scopeToSecretSanta($query)
    {
        return $query->where('type', 'to_secret_santa');
    }

    public function scopeToTarget($query)
    {
        return $query->where('type', 'to_target');
    }

    public function scopeReported($query)
    {
        return $query->where('is_reported', true);
    }

    public function scopeUnreviewed($query)
    {
        return $query->where('is_reviewed', false);
    }

    public function scopeForParticipant($query, int $participantId)
    {
        return $query->where(function ($q) use ($participantId) {
            $q->where('from_participant_id', $participantId)
                ->orWhere('to_participant_id', $participantId);
        });
    }

    /**
     * Accesseur pour contenu déchiffré
     */
    public function getContentAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromContext();
        return $masterKey ? $this->getDecryptedAttribute('content_encrypted', $masterKey) : null;
    }

    /**
     * Mutateur pour chiffrement du contenu
     */
    public function setContentAttribute(string $value): void
    {
        $masterKey = $this->getMasterKeyFromContext();
        if ($masterKey) {
            $this->setEncryptedAttribute('content_encrypted', $value, $masterKey);
        }
    }

    /**
     * Méthodes d'état
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
        return $this->is_reported;
    }

    public function isReviewed(): bool
    {
        return $this->is_reviewed;
    }

    /**
     * Actions sur le message
     */
    public function report(): void
    {
        $this->update(['is_reported' => true]);
    }

    public function markAsReviewed(string $notes = null): void
    {
        $this->update([
            'is_reviewed' => true,
            'reviewer_notes' => $notes,
        ]);
    }

    /**
     * Ajoute une réaction au message
     */
    public function addReaction(Participant $participant, string $reaction): MessageReaction
    {
        return $this->reactions()->updateOrCreate(
            ['participant_id' => $participant->id],
            ['reaction' => $reaction]
        );
    }

    /**
     * Supprime une réaction du message
     */
    public function removeReaction(Participant $participant): bool
    {
        return $this->reactions()
                ->where('participant_id', $participant->id)
                ->delete() > 0;
    }

    /**
     * Vérifie si un participant peut voir ce message
     */
    public function canBeSeenBy(Participant $participant): bool
    {
        return $this->from_participant_id === $participant->id
            || $this->to_participant_id === $participant->id;
    }

    /**
     * Vérifie si un participant peut répondre à ce message
     */
    public function canBeRepliedBy(Participant $participant): bool
    {
        // Seul le destinataire peut répondre
        if ($this->to_participant_id !== $participant->id) {
            return false;
        }

        // Vérifier les paramètres du tirage
        if (!$this->draw->allow_target_messages) {
            return false;
        }

        // Si c'est un message vers le secret santa, la cible peut répondre
        if ($this->isToSecretSanta()) {
            return true;
        }

        return false;
    }

    /**
     * Crée une réponse à ce message
     */
    public function createReply(string $content): self
    {
        return self::create([
            'draw_id' => $this->draw_id,
            'from_participant_id' => $this->to_participant_id,
            'to_participant_id' => $this->from_participant_id,
            'content' => $content,
            'type' => 'to_target', // La réponse va toujours vers la cible
        ]);
    }

    /**
     * Récupère la conversation entre deux participants
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
     * Récupère la clé master depuis le contexte
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implémenter selon le contexte
        return null;
    }
}
