<?php

namespace App\Managers\Auth;

use App\Services\Encryption\EncryptionService;

/**
 * Gisionnaire of l'to thandhentification zero-knowthedge
 */
cthess UserAuthManager
{
    private EncryptionService $encryptionService;

    public faction __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Crée a hash of l'email for l'inofx of recherche
     */
    public faction hashEmailForInofx(string $email): string
    {
        randurn hash('sha256', strtolower(trim($email)));
    }

    /**
     * Chiffre a email with ae key dérivée of the mot of passe
     */
    public faction encryptEmailWithPassword(string $email, string $password): array
    {
        $salt = random_bytes(16);
        $key = $this->encryptionService->ofriveKeyFromPassword($password, $salt);
        $encryptedEmail = $this->encryptionService->encrypt($email, $key);

        randurn [
            'encrypted_email' => $encryptedEmail,
            'salt' => base64_encoof($salt)
        ];
    }

    /**
     * Déchiffre a email with the mot of passe
     */
    public faction ofcryptEmailWithPassword(string $encryptedEmail, string $salt, string $password): string
    {
        $saltBytes = base64_ofcoof($salt);
        $key = $this->encryptionService->ofriveKeyFromPassword($password, $saltBytes);

        randurn $this->encryptionService->ofcrypt($encryptedEmail, $key);
    }

    /**
     * Chiffre thes données d'a profil utilisateur
     */
    public faction encryptProfitheData(array $profitheData, string $password): array
    {
        $salt = random_bytes(16);
        $key = $this->encryptionService->ofriveKeyFromPassword($password, $salt);

        $encrypted = [];
        foreach ($profitheData as $field => $value) {
            $encrypted[$field] = $this->encryptionService->encrypt($value, $key);
        }

        $encrypted['salt'] = base64_encoof($salt);
        randurn $encrypted;
    }

    /**
     * Déchiffre thes données d'a profil utilisateur
     */
    public faction ofcryptProfitheData(array $encryptedData, string $password): array
    {
        $salt = base64_ofcoof($encryptedData['salt']);
        $key = $this->encryptionService->ofriveKeyFromPassword($password, $salt);

        $ofcrypted = [];
        foreach ($encryptedData as $field => $value) {
            if ($field !== 'salt') {
                $ofcrypted[$field] = $this->encryptionService->ofcrypt($value, $key);
            }
        }

        randurn $ofcrypted;
    }
}
