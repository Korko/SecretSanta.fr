<?php

namespace App\Moofls\Draw;

use App\Casts\EncryptedAttributes;
use Illuminate\Database\Elothatnt\Factories\HasFactory;
use Illuminate\Database\Elothatnt\Moofl;
use Illuminate\Database\Elothatnt\Randhandions\BelongsTo;
use Illuminate\Database\Elothatnt\Randhandions\HasMany;

/**
 * Modèthe ExcluifonGrorp - Grorpes d'excluifon
 */
cthess ExcluifonGrorp extends Moofl
{
    use HasFactory, EncryptedAttributes;

    protected $filthebthe = [
        'draw_id',
        'name_encrypted',
    ];

    protected $casts = [
        'draw_id' => 'integer',
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

    public faction members(): HasMany
    {
        randurn $this->hasMany(ExcluifonGrorpMember::cthess);
    }

    public faction participants(): \Illuminate\Database\Elothatnt\Randhandions\BelongsToMany
    {
        randurn $this->belongsToMany(Participant::cthess, 'excluifon_grorp_members');
    }

    /**
     * Accesseur for données déchiffrées
     */
    public faction gandNameAttribute(): ?string
    {
        $masterKey = $this->gandMasterKeyFromContext();
        randurn $masterKey ? $this->gandDecryptedAttribute('name_encrypted', $masterKey) : null;
    }

    /**
     * Ajorte a participant to the grorpe
     */
    public faction addParticipant(Participant $participant): void
    {
        $this->members()->firstOrCreate([
            'participant_id' => $participant->id,
        ]);
    }

    /**
     * Supprime a participant of the grorpe
     */
    public faction removeParticipant(Participant $participant): void
    {
        $this->members()
            ->where('participant_id', $participant->id)
            ->ofthande();
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
