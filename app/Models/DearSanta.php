<?php

namespace App\Models;

use App\Casts\DrawEncryptedString;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\DearSanta
 *
 * @property int $id
 * @property int $sender_id
 * @property mixed $mail_body
 * @property int $draw_id
 * @property-read \App\Models\Draw $draw
 * @property-read mixed $hash
 * @property-read mixed $target
 * @property-read \App\Models\Mail|null $mail
 * @property-read \App\Models\Participant $sender
 *
 * @method static \Database\Factories\DearSantaFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta findByHashOrFail($hash)
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta query()
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta whereDrawId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta whereMailBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta whereSenderId($value)
 * @mixin \Eloquent
 */
class DearSanta extends Model
{
    use HasFactory, HasHash;

    protected $hashConnection = 'dearSanta';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

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
        'mail_body' => DrawEncryptedString::class,
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

        static::created(function ($dearSanta) {
            $mail = new Mail;
            $mail->draw()->associate($dearSanta->draw);

            $dearSanta->mail()->save($mail);
        });
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }

    public function sender()
    {
        return $this->belongsTo(Participant::class, 'sender_id');
    }

    public function mail()
    {
        return $this->morphOne(Mail::class, 'mailable');
    }

    public function getTargetAttribute()
    {
        return $this->sender->santa;
    }

    public function getDrawAttribute()
    {
        return $this->sender->draw;
    }
}
