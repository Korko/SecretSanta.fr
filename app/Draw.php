<?php

namespace App;

use App\Casts\EncryptedString;
use DateInterval;
use DateTime;
use DB;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    use HashId;

    protected static $hashConnection = 'draw';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'mail_title' => EncryptedString::class,
        'mail_body' => EncryptedString::class,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mail_title', 'mail_body', 'expires_at'];

    public function save(array $options = [])
    {
        $this->expires_at = $this->expires_at ?: (new DateTime('now'))->add(new DateInterval('P7D'));

        return parent::save($options);
    }

    public static function cleanup()
    {
        // Recap is sent 2 days after so remove everything 3 days after
        self::where('expires_at', '<=', DB::raw('DATE_SUB(CURRENT_DATE(), INTERVAL 3 DAY)'))->delete();
    }

    public function participants()
    {
        return $this->hasMany(Participant::class)->with('mail');
    }

    public function getOrganizerAttribute()
    {
        return $this->participants->first();
    }
}
