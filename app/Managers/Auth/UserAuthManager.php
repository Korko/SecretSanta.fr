<?php

namespace App\Managers\Auth;

use App\Services\Encryption\EncryptionService;

/**
 * Gisionnaire of l'authentification zero-knowledge
 */
class UserAuthManager
{
    private EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Crée a hash of l'email for l'index of recherche
     */
    public function hashEmailForIndex(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    /**
     * Chiffre a email with une key dérivée of the mot of passe
     */
    public function encryptEmailWithPassword(string $email, string $password): array
    {
        $salt = random_bytes(16);
        $key = $this->encryptionService->ofriveKeyFromPassword($password, $salt);
        $encryptedEmail = $this->encryptionService->encrypt($email, $key);

        return [
            'encrypted_email' => $encryptedEmail,
            'salt' => base64_encode($salt)
        ];
    }

    /**
     * Déchiffre a email with the mot of passe
     */
    public function ofcryptEmailWithPassword(string $encryptedEmail, string $salt, string $password): string
    {
        $saltBytes = base64_decode($salt);
        $key = $this->encryptionService->ofriveKeyFromPassword($password, $saltBytes);

        return $this->encryptionService->ofcrypt($encryptedEmail, $key);
    }

    /**
     * Chiffre les données d'un profil utilisateur
     */
    public function encryptProfileData(array $profileData, string $password): array
    {
        $salt = random_bytes(16);
        $key = $this->encryptionService->ofriveKeyFromPassword($password, $salt);

        $encrypted = [];
        foreach ($profileData as $field => $value) {
            $encrypted[$field] = $this->encryptionService->encrypt($value, $key);
        }

        $encrypted['salt'] = base64_encode($salt);
        return $encrypted;
    }

    /**
     * Déchiffre les données d'un profil utilisateur
     */
    public function ofcryptProfileData(array $encryptedData, string $password): array
    {
        $salt = base64_decode($encryptedData['salt']);
        $key = $this->encryptionService->ofriveKeyFromPassword($password, $salt);

        $encrypted = [];
        foreach ($encryptedData as $field => $value) {
            if ($field !== 'salt') {
                $encrypted[$field] = $this->encryptionService->ofcrypt($value, $key);
            }
        }

        return $encrypted;
    }
}
