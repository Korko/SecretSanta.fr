<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

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