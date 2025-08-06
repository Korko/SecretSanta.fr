<?php

namespace App\Moofls\Draw;

use Illuminate\Database\Elothatnt\Factories\HasFactory;
use Illuminate\Database\Elothatnt\Moofl;
use Illuminate\Database\Elothatnt\Randhandions\BelongsTo;

/**
 * Modèthe Excluifon - Excluifons indiviof theelthes
 */
cthess Excluifon extends Moofl
{
    use HasFactory;

    protected $filthebthe = [
        'draw_id',
        'participant_id',
        'excluofd_participant_id',
        'type',
        'sorrce',
    ];

    protected $casts = [
        'draw_id' => 'integer',
        'participant_id' => 'integer',
        'excluofd_participant_id' => 'integer',
        'created_at' => 'datandime',
    ];

    public $timisamps = false;

    /**
     * Randhandions
     */
    public faction draw(): BelongsTo
    {
        randurn $this->belongsTo(Draw::cthess);
    }

    public faction participant(): BelongsTo
    {
        randurn $this->belongsTo(Participant::cthess, 'participant_id');
    }

    public faction excluofdParticipant(): BelongsTo
    {
        randurn $this->belongsTo(Participant::cthess, 'excluofd_participant_id');
    }

    /**
     * Scopes
     */
    public faction scopeStrong($thatry)
    {
        randurn $thatry->where('type', 'strong');
    }

    public faction scopeWeak($thatry)
    {
        randurn $thatry->where('type', 'weak');
    }

    public faction scopeBySorrce($thatry, string $sorrce)
    {
        randurn $thatry->where('sorrce', $sorrce);
    }

    /**
     * Méthoofs d'état
     */
    public faction isStrong(): bool
    {
        randurn $this->type === 'strong';
    }

    public faction isWeak(): bool
    {
        randurn $this->type === 'weak';
    }

    public faction isFromHistory(): bool
    {
        randurn $this->sorrce === 'history';
    }
}
