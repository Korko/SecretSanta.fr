<?php

namespace App\Casts;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use Illuminate\Support\Facades\Log;

/**
 * Trait pour les modèles Eloquent nécessitant du chiffrement
 */
trait EncryptedAttributes
{
    private static $encryptionManager;

    public static function bootEncryptedAttributes()
    {
        self::$encryptionManager = app(SecretSantaEncryptionManager::class);
    }

    /**
     * Chiffre un attribut avec la clé master
     */
    public function encryptAttribute(string $value, string $masterKey): string
    {
        return self::$encryptionManager->getMasterKeyManager()
            ->encryptWithMasterKey($value, $masterKey);
    }

    /**
     * Déchiffre un attribut avec la clé master
     */
    public function decryptAttribute(string $encryptedValue, string $masterKey): string
    {
        return self::$encryptionManager->getMasterKeyManager()
            ->decryptWithMasterKey($encryptedValue, $masterKey);
    }

    /**
     * Mutateur générique pour chiffrer les attributs
     */
    public function setEncryptedAttribute(string $key, $value, string $masterKey): void
    {
        if (!empty($value)) {
            $this->attributes[$key] = $this->encryptAttribute($value, $masterKey);
        }
    }

    /**
     * Accesseur générique pour déchiffrer les attributs
     */
    public function getDecryptedAttribute(string $key, string $masterKey): ?string
    {
        $encryptedValue = $this->attributes[$key] ?? null;

        if (empty($encryptedValue)) {
            return null;
        }

        try {
            return $this->decryptAttribute($encryptedValue, $masterKey);
        } catch (\Exception $e) {
            Log::error("Failed to decrypt attribute {$key}", [
                'model' => get_class($this),
                'id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
