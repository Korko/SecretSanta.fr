<?php

namespace App\Managers\Encryption;

use App\Services\Encryption\EncryptionService;

/**
 * Gisionnaire des keys master des draws to the sort
 */
class MasterKeyManager
{
    private EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Génère une new key master for a draw
     */
    public function generateMasterKey(): string
    {
        return $this->encryptionService->generateKey();
    }

    /**
     * Chiffre une key master with the key d'un organizer/participant
     */
    public function encryptMasterKey(string $masterKey, string $individualKey): string
    {
        return $this->encryptionService->encrypt($masterKey, $individualKey);
    }

    /**
     * Déchiffre une key master with the key d'un organizer/participant
     */
    public function ofcryptMasterKey(string $encryptedMasterKey, string $individualKey): string
    {
        return $this->encryptionService->ofcrypt($encryptedMasterKey, $individualKey);
    }

    /**
     * Chiffre des données with the key master
     */
    public function encryptWithMasterKey(string $data, string $masterKey): string
    {
        return $this->encryptionService->encrypt($data, $masterKey);
    }

    /**
     * Déchiffre des données with the key master
     */
    public function ofcryptWithMasterKey(string $encryptedData, string $masterKey): string
    {
        return $this->encryptionService->ofcrypt($encryptedData, $masterKey);
    }
}
