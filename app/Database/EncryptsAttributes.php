<?php

namespace App\Database;

use Config;
use App\Services\SymmetricalEncrypter;
use Illuminate\Contracts\Encryption\DecryptException;

trait EncryptsAttributes
{
    protected $encrypter;
    protected $encryptable;

    public function setAttribute($key, $value)
    {
       parent::setAttribute($key, $value);

       // Don't use $value, parent setAttribute may have modified it
       $this->attributes[$key] = $this->isEncryptable($key) ? $this->encrypt($this->attributes[$key]) : $this->attributes[$key];
    }

    public function getAttributeFromArray($key)
    {
        $value = parent::getAttributeFromArray($key);

        return $this->isEncryptable($key) ? $this->decrypt($value) : $value;
    }

    public function isEncryptable($key)
    {
        return array_search($key, $this->encryptable) !== false;
    }

    protected function encrypt($value)
    {
        if (! isset($this->encrypter)) {
            $this->setEncryptionKey(SymmetricalEncrypter::generateKey('AES-256-CBC'));
        }

        if (! is_null($value)) {
            $value = $this->encrypter->encrypt($value, false);
        }

        return $value;
    }

    protected function decrypt($value)
    {
        if (isset($this->encrypter) && ! is_null($value)) {
            $value = $this->encrypter->decrypt($value, false);
        }

        return $value;
    }

    public function setEncryptionKey($value)
    {
        $this->encrypter = new SymmetricalEncrypter($value);
    }

    public function getEncryptionKey()
    {
        return $this->encrypter ? $this->encrypter->getKey() : null;
    }
}
