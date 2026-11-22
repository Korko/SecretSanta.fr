<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Participant $sender
 * @property-read Mail $mail
 * @property-read Draw $draw
 * @property string $mail_body
 */
#[Fillable(['mail_body', 'sender_id'])]
#[WithoutTimestamps]
final class DearSanta extends Model
{
    use HashId;
    use HasTrackedMail;

    /** @return BelongsTo<Participant, $this> */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'sender_id');
    }

    protected function getHashConnection(): string
    {
        return 'dearSanta';
    }

    /** @return Attribute<Draw, never> */
    protected function draw(): Attribute
    {
        return Attribute::make(get: fn (): Draw => $this->sender->draw);
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
