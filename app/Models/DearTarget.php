<?php

namespace App\Models;

use App\Enums\QuestionToSanta;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\DearTarget
 *
 * @property int $id
 * @property int $draw_id
 * @property int $sender_id
 * @property QuestionToSanta $mail_type
 * @property-read \App\Models\Draw $draw
 * @property-read mixed $hash
 * @property-read mixed $mail_body
 * @property-read mixed $target
 * @property-read \App\Models\Mail|null $mail
 * @property-read \App\Models\Participant $sender
 *
 * @method static \Database\Factories\DearTargetFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|DearTarget findByHashOrFail($hash)
 * @method static \Illuminate\Database\Eloquent\Builder|DearTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DearTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DearTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder|DearTarget whereDrawId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DearTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DearTarget whereMailType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DearTarget whereSenderId($value)
 * @mixin \Eloquent
 */
class DearTarget extends Model
{
    use HasFactory;

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

        static::created(function ($dearTarget) {
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
