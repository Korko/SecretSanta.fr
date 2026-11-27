<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A gift idea from a participant (sender) for another one (recipient), emailed to the recipient's santa:
 * the sender never learns who that santa is, and the santa never learns who the sender is (unless they say so in it)
 *
 * @property-read Participant|null $sender null once they left the draw (the idea stays)
 * @property-read Participant $recipient
 * @property-read Mail $mail
 * @property string $mail_body
 * @property int|null $sender_id
 * @property int $recipient_id
 */
#[Fillable(['mail_body'])]
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mail_body' => EncryptedString::class,
        ];
    }
}
