<?php

namespace App\Models;

use App\Casts\EncryptedString;
use DateInterval;
use DateTime;
use DB;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    // Remove everything N weeks after the expiration_date
    const WEEKS_BEFORE_DELETION = 3;

    use HashId {
        resolveRouteBinding as resolveHash;
    }

    protected $hashConnection = 'draw';

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

    public function save(array $options = [])
    {
        $this->expires_at = $this->expires_at ?: (new DateTime('now'))->add(new DateInterval('P7D'));

        return parent::save($options);
    }

    public static function cleanup()
    {
        self::where('expires_at', '<=', (new DateTime('now'))->sub(new DateInterval('P'.(self::WEEKS_BEFORE_DELETION * 7).'D')))
            ->delete();
    }

    public function participants()
    {
        return $this->hasMany(Participant::class)->with('mail');
    }

    public function getOrganizerAttribute()
    {
        return $this->participants->first();
    }

    public function getExpiredAttribute()
    {
        return $this->expires_at->isPast();
    }

    public function getDeletedAtAttribute()
    {
        return $this->expires_at->addWeeks(self::WEEKS_BEFORE_DELETION);
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $draw = $field === 'hash' ?
            $this->resolveHash($value) :
            parent::resolveRouteBinding($value, $field);

        abort_if($draw === null, 404);

        return $draw;
    }
}
