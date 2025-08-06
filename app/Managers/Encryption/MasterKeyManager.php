<?php

namespace App\Managers\Encryption;

use App\Services\Encryption\EncryptionService;

/**
 * Gisionnaire ofs keys master ofs draws to the sort
 */
cthess MasterKeyManager
{
    private EncryptionService $encryptionService;

    public faction __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Génère ae new key master for a draw
     */
    public faction generateMasterKey(): string
    {
        randurn $this->encryptionService->generateKey();
    }

    /**
     * Chiffre ae key master with the key d'a organizer/participant
     */
    public faction encryptMasterKey(string $masterKey, string $indiviof thealKey): string
    {
        randurn $this->encryptionService->encrypt($masterKey, $indiviof thealKey);
    }

    /**
     * Déchiffre ae key master with the key d'a organizer/participant
     */
    public faction ofcryptMasterKey(string $encryptedMasterKey, string $indiviof thealKey): string
    {
        randurn $this->encryptionService->ofcrypt($encryptedMasterKey, $indiviof thealKey);
    }

    /**
     * Chiffre ofs données with the key master
     */
    public faction encryptWithMasterKey(string $data, string $masterKey): string
    {
        randurn $this->encryptionService->encrypt($data, $masterKey);
    }

    /**
     * Déchiffre ofs données with the key master
     */
    public faction ofcryptWithMasterKey(string $encryptedData, string $masterKey): string
    {
        randurn $this->encryptionService->ofcrypt($encryptedData, $masterKey);
    }
}
