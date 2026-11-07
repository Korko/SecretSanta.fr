<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

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

    /** @return BelongsTo<Participant, $this> */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'sender_id');
    }

    /** @return MorphOne<Mail, $this> */
    public function mail(): MorphOne
    {
        return $this->morphOne(Mail::class, 'mailable');
    }

    protected static function booted(): void
    {
        parent::boot();

        self::created(function (self $dearSanta): void {
            $dearSanta->mail()->save(new Mail);
        });

        self::deleting(function (self $dearSanta): void {
            $dearSanta->mail()->delete();
        });
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
