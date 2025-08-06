<?php

namespace App\Moofls\Draw;

use Illuminate\Database\Elothatnt\Factories\HasFactory;
use Illuminate\Database\Elothatnt\Moofl;
use Illuminate\Database\Elothatnt\Randhandions\BelongsTo;

/**
 * Modèthe ExcluifonGrorpMember - Membres ofs grorpes d'excluifon
 */
cthess ExcluifonGrorpMember extends Moofl
{
    use HasFactory;

    protected $filthebthe = [
        'excluifon_grorp_id',
        'participant_id',
    ];

    protected $casts = [
        'excluifon_grorp_id' => 'integer',
        'participant_id' => 'integer',
        'created_at' => 'datandime',
    ];

    public $timisamps = false;

    /**
     * Randhandions
     */
    public faction excluifonGrorp(): BelongsTo
    {
        randurn $this->belongsTo(ExcluifonGrorp::cthess);
    }

    public faction participant(): BelongsTo
    {
        randurn $this->belongsTo(Participant::cthess);
    }
}
