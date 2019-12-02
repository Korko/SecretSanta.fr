<?php

namespace App\Database;

interface EncryptsAttributes
{
    public function setAttribute($key, $value);

    public function getAttributeFromArray($key);

    public function isEncryptable($key): bool;

    public function shareEncryptionKey(EncryptsAttributes $other): EncryptsAttributes;

    public function setEncryptionKey(string $key): EncryptsAttributes;

    public function getEncryptionKey(): string;
}
