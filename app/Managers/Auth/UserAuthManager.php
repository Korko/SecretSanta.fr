<?php

namespace App\Managers\Auth;

use App\Services\Encryption\EncryptionService;

/**
 * Gestionnaire de l'authentification zero-knowledge
 */
class UserAuthManager
{
    private EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Crée un hash de l'email pour l'index de recherche
     */
    public function hashEmailForIndex(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    /**
     * Chiffre un email avec une clé dérivée du mot de passe
     */
    public function encryptEmailWithPassword(string $email, string $password): array
    {
        $salt = random_bytes(16);
        $key = $this->encryptionService->deriveKeyFromPassword($password, $salt);
        $encryptedEmail = $this->encryptionService->encrypt($email, $key);

        return [
            'encrypted_email' => $encryptedEmail,
            'salt' => base64_encode($salt)
        ];
    }

    /**
     * Déchiffre un email avec le mot de passe
     */
    public function decryptEmailWithPassword(string $encryptedEmail, string $salt, string $password): string
    {
        $saltBytes = base64_decode($salt);
        $key = $this->encryptionService->deriveKeyFromPassword($password, $saltBytes);

        return $this->encryptionService->decrypt($encryptedEmail, $key);
    }

    /**
     * Chiffre les données d'un profil utilisateur
     */
    public function encryptProfileData(array $profileData, string $password): array
    {
        $salt = random_bytes(16);
        $key = $this->encryptionService->deriveKeyFromPassword($password, $salt);

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
    public function decryptProfileData(array $encryptedData, string $password): array
    {
        $salt = base64_decode($encryptedData['salt']);
        $key = $this->encryptionService->deriveKeyFromPassword($password, $salt);

        $decrypted = [];
        foreach ($encryptedData as $field => $value) {
            if ($field !== 'salt') {
                $decrypted[$field] = $this->encryptionService->decrypt($value, $key);
            }
        }

        return $decrypted;
    }
}
