<?php

namespace App\Models;

use App\Casts\EncryptedString;

class DearSanta extends Model
{
    use HashId;

    protected $hashConnection = 'dearSanta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mail_body', 'sender_id', 'mail_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'mail_body' => EncryptedString::class,
    ];

    protected static function booted()
    {
        parent::boot();

        static::created(function($dearSanta) {
            $dearSanta->mail()->save(new Mail);
        });

        static::deleting(function ($dearSanta) {
            $dearSanta->mail->delete();
        });
    }

    public function sender()
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
