<?php

namespace App\Models\Message;

use App\Models\Draw\Participant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MessageReaction Model - Reactions to messages
 */
class MessageReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'participant_id',
        'reaction',
    ];

    protected $casts = [
        'message_id' => 'integer',
        'participant_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public $timestamp = false;

    /**
     * Relations
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * List of allowed reactions
     */
    public static function getAllowedReactions(): array
    {
        return [
            '👍', '👎', '❤️', '😊', '😂',
            '😮', '😢', '😡', '👏', '🎉',
            '🎁', '🎄', '⭐', '✨', '🔥'
        ];
    }

    /**
     * Validate a reaction
     */
    public static function isValidReaction(string $reaction): bool
    {
        return in_array($reaction, self::getAllowedReactions());
    }
}
