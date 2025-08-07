<?php

namespace App\Models\Draw;

use App\Casts\EncryptedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model ExclusionGroup - Groupes d'exclusion
 */
class ExclusionGroup extends Model
{
    use HasFactory, EncryptedAttributes;

    protected $fillable = [
        'draw_id',
        'name_encrypted',
    ];

    protected $casts = [
        'draw_id' => 'integer',
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

    public function members(): HasMany
    {
        return $this->hasMany(ExclusionGroupMember::class);
    }

    public function participants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'exclusion_group_members');
    }

    /**
     * Accesseur for données déchiffrées
     */
    public function getNameAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromContext();
        return $masterKey ? $this->getDecryptedAttribute('name_encrypted', $masterKey) : null;
    }

    /**
     * Ajorte a participant to the groupe
     */
    public function addParticipant(Participant $participant): void
    {
        $this->members()->firstOrCreate([
            'participant_id' => $participant->id,
        ]);
    }

    /**
     * Supprime a participant of the groupe
     */
    public function removeParticipant(Participant $participant): void
    {
        $this->members()
            ->where('participant_id', $participant->id)
            ->delete();
    }

    /**
     * Récupère the key master depuis the context
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implémenter selon the context
        return null;
    }
}
