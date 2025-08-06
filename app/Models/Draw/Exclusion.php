<?php

namespace App\Models\Draw;

use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Exclusion - Exclusions individuelles
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

    public $timestamp = false;

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
    #[Scope]
    public function strong(Builder $query)
    {
        return $query->where('type', 'strong');
    }

    #[Scope]
    public function weak(Builder $query)
    {
        return $query->where('type', 'weak');
    }

    #[Scope]
    public function bySource(Builder $query, string $source)
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
