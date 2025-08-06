<?php

namespace App\Models\Draw;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ExclusionGroupMember - Membres des groupes d'exclusion
 */
class ExclusionGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'exclusion_group_id',
        'participant_id',
    ];

    protected $casts = [
        'exclusion_group_id' => 'integer',
        'participant_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public $timestamp = false;

    /**
     * Relations
     */
    public function exclusionGroup(): BelongsTo
    {
        return $this->belongsTo(ExclusionGroup::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
}
