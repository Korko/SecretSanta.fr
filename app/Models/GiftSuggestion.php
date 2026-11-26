<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A gift idea from a participant (sender) for another one (recipient), emailed to the recipient's santa:
 * the sender never learns who that santa is, and the santa learns who the sender is only when the idea is signed
 *
 * @property-read Participant|null $sender null once they left the draw (the idea stays)
 * @property-read Participant $recipient
 * @property-read Mail $mail
 * @property string $mail_body
 * @property int|null $sender_id
 * @property int $recipient_id
 * @property bool $signed the sender chose to show their name to the recipient's santa
 */
#[Fillable(['mail_body', 'signed'])]
#[WithoutTimestamps]
final class GiftSuggestion extends Model
{
    use HasTrackedMail;

    /** @return BelongsTo<Participant, $this> */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'sender_id');
    }

    /** @return BelongsTo<Participant, $this> */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'recipient_id');
    }

    // What the recipient's santa may read as its author: the sender's name when signed, null ("?") otherwise,
    // also once the sender left the draw
    public function signature(): ?string
    {
        return $this->signed ? $this->sender?->name : null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mail_body' => EncryptedString::class,
            'signed' => 'boolean',
        ];
    }
}
