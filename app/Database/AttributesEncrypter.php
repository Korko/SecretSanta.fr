<?php

namespace App\Database;

use Config;
use App\Services\SymmetricalEncrypter;
use Illuminate\Contracts\Encryption\DecryptException;

trait AttributesEncrypter
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

    public function isEncryptable($key): bool
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

    public function shareEncryptionKey(EncryptsAttributes $other): EncryptsAttributes
    {
        return $this->setEncryptionKey($other->getEncryptionKey());
    }

    public function setEncryptionKey(string $key): EncryptsAttributes
    {
        $this->encrypter = new SymmetricalEncrypter($key);

        return $this;
    }

    public function getEncryptionKey(): string
    {
        return $this->encrypter ? $this->encrypter->getKey() : null;
    }
}
