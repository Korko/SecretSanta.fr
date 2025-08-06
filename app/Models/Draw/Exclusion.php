<?php

namespace App\Models\Draw;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Exclusion - Exclusions individuelles
 */
class Exclusion extends Model
{
    use HasFactory;

    protected $fillable = [
        'draw_id',
        'participant_id',
        'excluded_participant_id',
        'type',
        'source',
    ];

    protected $casts = [
        'draw_id' => 'integer',
        'participant_id' => 'integer',
        'excluded_participant_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Relations
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function excludedParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'excluded_participant_id');
    }

    /**
     * Scopes
     */
    public function scopeStrong($query)
    {
        return $query->where('type', 'strong');
    }

    public function scopeWeak($query)
    {
        return $query->where('type', 'weak');
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Méthodes d'état
     */
    public function isStrong(): bool
    {
        return $this->type === 'strong';
    }

    public function isWeak(): bool
    {
        return $this->type === 'weak';
    }

    public function isFromHistory(): bool
    {
        return $this->source === 'history';
    }
}
