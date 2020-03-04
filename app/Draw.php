<?php

namespace App;

use App\Casts\EncryptedString;
use DateInterval;
use DateTime;
use DB;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    protected $dates = [
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_title' => EncryptedString::class,
        'email_body' => EncryptedString::class,
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
