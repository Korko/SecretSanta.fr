<?php

namespace App\Models;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DearSanta extends Model
{
    use HasFactory, HashId;

    protected $hashConnection = 'dearSanta';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['mail_body', 'draw_id', 'sender_id'];

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

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['sender'];

    protected static function booted()
    {
        parent::boot();

        static::created(function($dearSanta) {
            $mail = new Mail;
            $mail->draw()->associate($dearSanta->draw);

            $dearSanta->mail()->save($mail);
        });
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class, 'draw_id');
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
