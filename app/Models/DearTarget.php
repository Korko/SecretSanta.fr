<?php

namespace App\Models;

use App\Enums\QuestionToSanta;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DearTarget extends Model
{
    use HasFactory, HashId;

    protected $hashConnection = 'dearTarget';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['draw_id', 'mail_type', 'sender_id'];

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
        'mail_type' => QuestionToSanta::class,
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

        static::created(function($dearTarget) {
            $mail = new Mail();
            $mail->draw()->associate($dearTarget->draw);
            $dearTarget->mail()->save($mail);
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
        return $this->sender->target;
    }

    public function getMailBodyAttribute()
    {
        return $this->mail_type->body();
    }
}
