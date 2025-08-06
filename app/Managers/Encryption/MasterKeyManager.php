<?php

namespace App\Managers\Encryption;

use App\Services\Encryption\EncryptionService;

/**
 * Gestionnaire des clés master des tirages au sort
 */
class MasterKeyManager
{
    private EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Génère une nouvelle clé master pour un tirage
     */
    public function generateMasterKey(): string
    {
        return $this->encryptionService->generateKey();
    }

    /**
     * Chiffre une clé master avec la clé d'un organisateur/participant
     */
    public function encryptMasterKey(string $masterKey, string $individualKey): string
    {
        return $this->encryptionService->encrypt($masterKey, $individualKey);
    }

    /**
     * Déchiffre une clé master avec la clé d'un organisateur/participant
     */
    public function decryptMasterKey(string $encryptedMasterKey, string $individualKey): string
    {
        return $this->encryptionService->decrypt($encryptedMasterKey, $individualKey);
    }

    /**
     * Chiffre des données avec la clé master
     */
    public function encryptWithMasterKey(string $data, string $masterKey): string
    {
        return $this->encryptionService->encrypt($data, $masterKey);
    }

    /**
     * Déchiffre des données avec la clé master
     */
    public function decryptWithMasterKey(string $encryptedData, string $masterKey): string
    {
        return $this->encryptionService->decrypt($encryptedData, $masterKey);
    }
}
