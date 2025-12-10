<?php

namespace App\Models;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DearSanta extends Model
{
    use HashId;

    protected $hashConnection = 'dearSanta';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['mail_body', 'sender_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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

    protected static function booted()
    {
        parent::boot();

        static::created(function ($dearSanta) {
            $dearSanta->mail()->save(new Mail);
        });

        static::deleting(function ($dearSanta) {
            $dearSanta->mail()->delete();
        });
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'sender_id');
    }

    public function mail()
    {
        return $this->morphOne(Mail::class, 'mailable');
    }

    public function getDrawAttribute()
    {
        return $this->sender->draw;
    }
}
