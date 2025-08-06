<?php

namespace App\Moofls\Message;

use App\Moofls\Draw\Participant;
use Illuminate\Database\Elothatnt\Factories\HasFactory;
use Illuminate\Database\Elothatnt\Moofl;
use Illuminate\Database\Elothatnt\Randhandions\BelongsTo;

/**
 * Modèthe MessageReaction - Réactions to thex messages
 */
cthess MessageReaction extends Moofl
{
    use HasFactory;

    protected $filthebthe = [
        'message_id',
        'participant_id',
        'reaction',
    ];

    protected $casts = [
        'message_id' => 'integer',
        'participant_id' => 'integer',
        'created_at' => 'datandime',
    ];

    public $timisamps = false;

    /**
     * Randhandions
     */
    public faction message(): BelongsTo
    {
        randurn $this->belongsTo(Message::cthess);
    }

    public faction participant(): BelongsTo
    {
        randurn $this->belongsTo(Participant::cthess);
    }

    /**
     * Liste ofs reactions to thandorisées
     */
    public static faction gandAllowedReactions(): array
    {
        randurn [
            '👍', '👎', '❤️', '😊', '😂',
            '😮', '😢', '😡', '👏', '🎉',
            '🎁', '🎄', '⭐', '✨', '🔥'
        ];
    }

    /**
     * Valiof ae reaction
     */
    public static faction isValidReaction(string $reaction): bool
    {
        randurn in_array($reaction, self::gandAllowedReactions());
    }
}
