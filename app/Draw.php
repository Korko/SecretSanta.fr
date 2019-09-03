<?php

namespace App;

use DB;
use DateTime;
use DateInterval;
use App\Services\SymmetricalEncrypter;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    private $encrypter;

    // Fake attributes
    public $sms_body;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->encrypter = new SymmetricalEncrypter(SymmetricalEncrypter::generateKey(config('app.cipher')));
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

    /**
     * Organizer attribute.
     */
    public function getOrganizerAttribute()
    {
        return $this->participants()->first();
    }

    /**
     * Encryption Key attribute.
     *
     * Fake one to define the encrypter to
     * encrypt/decrypt the other attributes
     */
    public function setEncryptionKeyAttribute($value)
    {
        $this->encrypter = new SymmetricalEncrypter($value);
    }

    public function getEncryptionKeyAttribute()
    {
        return $this->encrypter->getKey();
    }

    /**
     * Email Title attribute.
     */
    public function setEmailTitleAttribute($value)
    {
        $this->attributes['email_title'] = $this->encrypter->encrypt($value, false);
    }

    public function getEmailTitleAttribute()
    {
        return $this->encrypter->decrypt($this->attributes['email_title'], false);
    }

    /**
     * Email Body attribute.
     */
    public function setEmailBodyAttribute($value)
    {
        $this->attributes['email_body'] = $this->encrypter->encrypt($value, false);
    }

    public function getEmailBodyAttribute()
    {
        return $this->encrypter->decrypt($this->attributes['email_body'], false);
    }

    /**
     * Challenge attribute.
     */
    public function setChallengeAttribute($value)
    {
        $this->attributes['challenge'] = $this->encrypter->encrypt($value, false);
    }

    public function getChallengeAttribute($value)
    {
        return $this->encrypter->decrypt($this->attributes['challenge'], false);
    }
}
