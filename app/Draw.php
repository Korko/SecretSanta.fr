<?php

namespace App;

use DB;
use DateTime;
use DateInterval;
use App\Database\Model;

class Draw extends Model
{
    protected $dates = [
        'expires_at',
    ];

    protected $encrypted = [
        'email_title',
        'email_body',
        'challenge',
    ];

    // Fake attributes
    public $sms_body;

    public function save(array $options = [])
    {
        $this->challenge = config('app.challenge');
        $this->expires_at = $this->expires_at ?: (new DateTime('now'))->add(new DateInterval('P7D'));

        return parent::save($options);
    }

    public static function cleanup()
    {
        self::where('expires_at', '<=', DB::raw('CURRENT_TIMESTAMP'))->delete();
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function dearSanta()
    {
        return $this->hasMany(DearSanta::class);
    }

    public function getOrganizerAttribute()
    {
        return $this->participants->first();
    }
}
