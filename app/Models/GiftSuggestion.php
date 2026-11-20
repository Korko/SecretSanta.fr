<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * A gift idea from a participant (sender) for another one (recipient), emailed to the recipient's santa:
 * the sender never learns who that santa is, and only shows their name if they chose to sign
 *
 * @property-read Participant|null $sender null once they left the draw (the idea stays, unsigned)
 * @property-read Participant $recipient
 * @property-read Mail $mail
 * @property string $mail_body
 * @property bool $signed
 * @property int|null $sender_id
 * @property int $recipient_id
 */
#[Fillable(['mail_body', 'signed'])]
#[WithoutTimestamps]
final class GiftSuggestion extends Model
{
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

    /** @return MorphOne<Mail, $this> */
    public function mail(): MorphOne
    {
        return $this->morphOne(Mail::class, 'mailable');
    }

    protected static function booted(): void
    {
        self::created(function (self $suggestion): void {
            $suggestion->mail()->save(new Mail);
        });

        self::deleting(function (self $suggestion): void {
            $suggestion->mail()->delete();
        });
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
