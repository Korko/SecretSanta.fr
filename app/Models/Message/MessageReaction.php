<?php

namespace App\Models\Message;

use App\Models\Draw\Participant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle MessageReaction - Réactions aux messages
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

    public $timestamps = false;

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
     * Liste des réactions autorisées
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
     * Valide une réaction
     */
    public static function isValidReaction(string $reaction): bool
    {
        return in_array($reaction, self::getAllowedReactions());
    }
}
