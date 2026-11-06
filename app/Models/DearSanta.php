<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Participant $sender
 */
#[Fillable(['mail_body', 'sender_id'])]
#[WithoutTimestamps]
final class DearSanta extends Model
{
    use HasFactory;
    use HashId;

    protected $hashConnection = 'dearSanta';

    /** @return BelongsTo<Participant, $this> */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'sender_id');
    }

    public function mail()
    {
        return $this->morphOne(Mail::class, 'mailable');
    }

    protected static function booted(): void
    {
        parent::boot();

        self::created(function ($dearSanta): void {
            $dearSanta->mail()->save(new Mail);
        });

        self::deleting(function ($dearSanta): void {
            $dearSanta->mail()->delete();
        });
    }

    protected function draw(): Attribute
    {
        return Attribute::make(get: fn () => $this->sender->draw);
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
