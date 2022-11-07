<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\Exclusion
 *
 * @property int $participant_id
 * @property int $exclusion_id
 * @property-read Exclusion $exclusion
 * @property-read Exclusion $participant
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Exclusion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exclusion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exclusion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exclusion whereExclusionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exclusion whereParticipantId($value)
 * @mixin \Eloquent
 */
class Exclusion extends Pivot
{
    public $table = 'exclusions';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function participant()
    {
        return $this->belongsTo(self::class, 'participant_id');
    }

    public function exclusion()
    {
        return $this->belongsTo(self::class, 'exclusion_id');
    }
}
