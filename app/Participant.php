<?php

namespace App;

use App\Services\SymmetricalEncrypter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Participant extends Model
{
    use Notifiable;

    protected $encrypter;

    // Fake attributes
    public $phone_number;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'delivery_status' => self::CREATED,
    ];

    const CREATED = 'created';
    const SENT = 'sent';
    const RECEIVED = 'received';
    const ERROR = 'error';

    public static $deliveryStatuses = [
         self::CREATED,
         self::SENT,
         self::RECEIVED,
         self::ERROR,
    ];

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }

    public function routeNotificationForMail()
    {
        return [$this->email_address => $this->name];
    }

    public function routeNotificationForSms()
    {
        return $this->phone_number;
    }

    /**
     * Encryption Key attribute
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
     * Name attribute
     */

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $this->encrypter->encrypt($value, false);
    }

    public function getNameAttribute()
    {
        return $this->encrypter->decrypt($this->attributes['name'], false);
    }

    /**
     * Email Address attribute
     */

    public function setEmailAddressAttribute($value)
    {
        $this->attributes['email_address'] = $this->encrypter->encrypt($value, false);
    }

    public function getEmailAddressAttribute()
    {
        return $this->encrypter->decrypt($this->attributes['email_address'], false);
    }

    /**
     * Target attribute
     */

    public function setTargetAttribute($value)
    {
        $this->attributes['target'] = $this->encrypter->encrypt(json_encode($value), false);
    }

    public function getTargetAttribute()
    {
        return json_decode($this->encrypter->decrypt($this->attributes['target'], false));
    }

}
