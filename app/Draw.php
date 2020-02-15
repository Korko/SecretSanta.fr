<?php

namespace App;

use App\Database\Model;
use DateInterval;
use DateTime;
use DB;

class Draw extends Model
{
    protected $dates = [
        'expires_at',
    ];

    protected $encrypted = [
        'email_title',
        'email_body',
    ];

    public function save(array $options = [])
    {
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

    public function getOrganizerAttribute()
    {
        return $this->participants->first();
    }
}
