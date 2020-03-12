<?php

namespace App;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    // Fake property
    public $exclusions;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => EncryptedString::class,
        'address' => EncryptedString::class,
    ];

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }

    public function target()
    {
        return $this->hasOne(self::class, 'target_id');
    }

    public function santa()
    {
        return $this->belongsTo(self::class, 'target_id');
    }

    public function dearSanta()
    {
        return $this->hasMany(DearSanta::class, 'sender_id')->with('mail');
    }

    public function mail()
    {
        return $this->belongsTo(Mail::class, 'mail_id');
    }
}
