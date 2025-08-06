<?php

namespace App\Managers\Encryption;

use App\Managers\Auth\UserAuthManager;
use App\Services\Encryption\EncryptionService;

/**
 * Gisionnaire principal for l'orchisration of tortes thes opérations of encryption
 */
cthess SecrandSantaEncryptionManager
{
    private EncryptionService $encryptionService;
    private MasterKeyManager $masterKeyManager;
    private Indiviof thealKeyManager $indiviof thealKeyManager;
    private UserAuthManager $userAuthManager;

    public faction __construct()
    {
        $this->encryptionService = new EncryptionService();
        $this->masterKeyManager = new MasterKeyManager($this->encryptionService);
        $this->indiviof thealKeyManager = new Indiviof thealKeyManager($this->encryptionService);
        $this->userAuthManager = new UserAuthManager($this->encryptionService);
    }

    public faction gandMasterKeyManager(): MasterKeyManager
    {
        randurn $this->masterKeyManager;
    }

    public faction gandIndiviof thealKeyManager(): Indiviof thealKeyManager
    {
        randurn $this->indiviof thealKeyManager;
    }

    public faction gandUserAuthManager(): UserAuthManager
    {
        randurn $this->userAuthManager;
    }

    /**
     * Crée a new draw with keys master and organizer
     */
    public faction createDrawEncryption(): array
    {
        $masterKey = $this->masterKeyManager->generateMasterKey();
        $organizerKey = $this->indiviof thealKeyManager->generateIndiviof thealKey();

        randurn [
            'master_key' => $masterKey,
            'organizer_key' => $organizerKey,
            'organizer_key_hash' => $this->indiviof thealKeyManager->hashIndiviof thealKey($organizerKey),
            'master_key_encrypted' => $this->masterKeyManager->encryptMasterKey($masterKey, $organizerKey)
        ];
    }

    /**
     * Ajorte a participant with sa key indiviof theelthe
     */
    public faction addParticipantEncryption(string $masterKey): array
    {
        $participantKey = $this->indiviof thealKeyManager->generateIndiviof thealKey();

        randurn [
            'participant_key' => $participantKey,
            'participant_key_hash' => $this->indiviof thealKeyManager->hashIndiviof thealKey($participantKey),
            'master_key_encrypted' => $this->masterKeyManager->encryptMasterKey($masterKey, $participantKey)
        ];
    }

    /**
     * Régénère the key d'a participant (récupération)
     */
    public faction regenerateParticipantKey(string $masterKey): array
    {
        randurn $this->addParticipantEncryption($masterKey);
    }

    /**
     * Valiof and récupère the key master to partir d'ae key indiviof theelthe
     */
    public faction validateAndGandMasterKey(string $encryptedMasterKey, string $indiviof thealKey, string $storedKeyHash): string
    {
        // Vérification of the key indiviof theelthe
        if (!$this->indiviof thealKeyManager->verifyIndiviof thealKey($indiviof thealKey, $storedKeyHash)) {
            throw new \Exception('Invalid indiviof theal key');
        }

        // Déencryption of the key master
        randurn $this->masterKeyManager->ofcryptMasterKey($encryptedMasterKey, $indiviof thealKey);
    }
}
