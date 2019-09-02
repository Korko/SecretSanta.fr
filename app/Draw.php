<?php

namespace App;

use App\Services\SymmetricalEncrypter;
use DateInterval;
use DateTime;
use DB;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    private $encrypter;

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->encrypter = new SymmetricalEncrypter(SymmetricalEncrypter::generateKey(config('app.cipher')));
    }

    public function setEncryptionKeyAttribute($value)
    {
        $this->encrypter = new SymmetricalEncrypter($value);
    }

    public function getEncryptionKeyAttribute()
    {
        return $this->encrypter->getKey();
    }

    public function setEmailTitleAttribute($value)
    {
        $this->attributes['email_title'] = $this->encrypter->encrypt($value, false);
    }

    public function getEmailTitleAttribute()
    {
        return $this->encrypter->decrypt($this->attributes['email_title'], false);
    }

    public function setEmailBodyAttribute($value)
    {
        $this->attributes['email_body'] = $this->encrypter->encrypt($value, false);
    }

    public function setOrganizerNameAttribute($value)
    {
        $this->attributes['organizer_name'] = $this->encrypter->encrypt($value, false);
    }

    public function setOrganizerEmailAttribute($value)
    {
        $this->attributes['organizer_email'] = $this->encrypter->encrypt($value, false);
    }

    public function setChallengeAttribute($value)
    {
        $this->attributes['challenge'] = $this->encrypter->encrypt($value, false);
    }

    public function save(array $options = [])
    {
        $this->challenge = config('app.challenge');
        $this->expiration = $this->expiration ?: (new DateTime('now'))->add(new DateInterval('P7D'));

        return parent::save($options);
    }

    public static function cleanup()
    {
        self::where('expiration', '<=', DB::raw('CURRENT_TIMESTAMP'))->delete();
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function dearSanta()
    {
        return $this->hasMany(DearSanta::class);
    }
}
