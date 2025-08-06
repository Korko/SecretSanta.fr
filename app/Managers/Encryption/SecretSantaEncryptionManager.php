<?php

namespace App\Managers\Encryption;

use App\Managers\Auth\UserAuthManager;
use App\Services\Encryption\EncryptionService;

/**
 * Gestionnaire principal pour l'orchestration de toutes les opérations de chiffrement
 */
class SecretSantaEncryptionManager
{
    private EncryptionService $encryptionService;
    private MasterKeyManager $masterKeyManager;
    private IndividualKeyManager $individualKeyManager;
    private UserAuthManager $userAuthManager;

    public function __construct()
    {
        $this->encryptionService = new EncryptionService();
        $this->masterKeyManager = new MasterKeyManager($this->encryptionService);
        $this->individualKeyManager = new IndividualKeyManager($this->encryptionService);
        $this->userAuthManager = new UserAuthManager($this->encryptionService);
    }

    public function getMasterKeyManager(): MasterKeyManager
    {
        return $this->masterKeyManager;
    }

    public function getIndividualKeyManager(): IndividualKeyManager
    {
        return $this->individualKeyManager;
    }

    public function getUserAuthManager(): UserAuthManager
    {
        return $this->userAuthManager;
    }

    /**
     * Crée un nouveau tirage avec clés master et organisateur
     */
    public function createDrawEncryption(): array
    {
        $masterKey = $this->masterKeyManager->generateMasterKey();
        $organizerKey = $this->individualKeyManager->generateIndividualKey();

        return [
            'master_key' => $masterKey,
            'organizer_key' => $organizerKey,
            'organizer_key_hash' => $this->individualKeyManager->hashIndividualKey($organizerKey),
            'master_key_encrypted' => $this->masterKeyManager->encryptMasterKey($masterKey, $organizerKey)
        ];
    }

    /**
     * Ajoute un participant avec sa clé individuelle
     */
    public function addParticipantEncryption(string $masterKey): array
    {
        $participantKey = $this->individualKeyManager->generateIndividualKey();

        return [
            'participant_key' => $participantKey,
            'participant_key_hash' => $this->individualKeyManager->hashIndividualKey($participantKey),
            'master_key_encrypted' => $this->masterKeyManager->encryptMasterKey($masterKey, $participantKey)
        ];
    }

    /**
     * Régénère la clé d'un participant (récupération)
     */
    public function regenerateParticipantKey(string $masterKey): array
    {
        return $this->addParticipantEncryption($masterKey);
    }

    /**
     * Valide et récupère la clé master à partir d'une clé individuelle
     */
    public function validateAndGetMasterKey(string $encryptedMasterKey, string $individualKey, string $storedKeyHash): string
    {
        // Vérification de la clé individuelle
        if (!$this->individualKeyManager->verifyIndividualKey($individualKey, $storedKeyHash)) {
            throw new \Exception('Invalid individual key');
        }

        // Déchiffrement de la clé master
        return $this->masterKeyManager->decryptMasterKey($encryptedMasterKey, $individualKey);
    }
}
