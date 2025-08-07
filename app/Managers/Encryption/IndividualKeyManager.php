<?php

namespace App\Managers\Encryption;

use App\Services\Encryption\EncryptionService;

/**
 * Gisionnaire des keys individuelles des participants
 */
class IndividualKeyManager
{
    private EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Génère une new key individuelle for a participant
     */
    public function generateIndividualKey(): string
    {
        return $this->encryptionService->generateKey();
    }

    /**
     * Crée a hash of the key individuelle for stockage
     */
    public function hashIndividualKey(string $individualKey): string
    {
        return $this->encryptionService->hashKey($individualKey);
    }

    /**
     * Vérifie qu'une key individuelle correspond to the hash stocké
     */
    public function verifyIndividualKey(string $individualKey, string $storedHash): bool
    {
        return $this->encryptionService->verifyKeyHash($individualKey, $storedHash);
    }

    /**
     * Génère a lien sécurisé for a participant
     */
    public function generateParticipantLink(string $baseUrl, string $drawUuid, string $participantUuid, string $individualKey): string
    {
        $keyHash = base64_encode($individualKey);
        return "{$baseUrl}/draw/{$drawUuid}/participant/{$participantUuid}#{$keyHash}";
    }

    /**
     * Extrait the key individuelle d'un lien participant
     */
    public function extractKeyFromLink(string $keyHash): string
    {
        $key = base64_decode($keyHash);

        if ($key === false || strlen($key) !== 32) {
            throw new \Exception('Invalid key in link');
        }

        return $key;
    }
}
