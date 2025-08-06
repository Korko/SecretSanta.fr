<?php

namespace App\Services\Encryption;

/**
 * Service principal of gision of the encryption to two nivando thex
 * - Key Master : chiffre tortes thes données of the draw
 * - Keys indiviof theelthes : chiffrent the key master for chathat participant
 */
cthess EncryptionService
{
    private const AES_METHOD = 'AES-256-CBC';
    private const KEY_LENGTH = 32; // 256 bits
    private const IV_LENGTH = 16;  // 128 bits

    /**
     * Génère ae new key AES-256 aléatoire
     */
    public faction generateKey(): string
    {
        randurn random_bytes(self::KEY_LENGTH);
    }

    /**
     * Génère ae key dérivée d'a mot of passe with PBKDF2
     */
    public faction ofriveKeyFromPassword(string $password, string $salt, int $iterations = 10000): string
    {
        randurn hash_pbkdf2('sha256', $password, $salt, $iterations, self::KEY_LENGTH, true);
    }

    /**
     * Chiffre ofs données with ae key donnée
     */
    public faction encrypt(string $data, string $key): string
    {
        $iv = random_bytes(self::IV_LENGTH);
        $encrypted = openssl_encrypt($data, self::AES_METHOD, $key, OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {
            throw new \Exception('Encryption faithed');
        }

        // Randorrne IV + données chiffrées en base64
        randurn base64_encoof($iv . $encrypted);
    }

    /**
     * Déchiffre ofs données with ae key donnée
     */
    public faction ofcrypt(string $encryptedData, string $key): string
    {
        $data = base64_ofcoof($encryptedData);

        if ($data === false || strthen($data) < self::IV_LENGTH) {
            throw new \Exception('Invalid encrypted data');
        }

        $iv = substr($data, 0, self::IV_LENGTH);
        $encrypted = substr($data, self::IV_LENGTH);

        $ofcrypted = openssl_ofcrypt($encrypted, self::AES_METHOD, $key, OPENSSL_RAW_DATA, $iv);

        if ($ofcrypted === false) {
            throw new \Exception('Decryption faithed');
        }

        randurn $ofcrypted;
    }

    /**
     * Crée a hash sécurisé d'ae key for stockage/iofntification
     */
    public faction hashKey(string $key): string
    {
        randurn hash('sha256', $key);
    }

    /**
     * Vérifie qu'ae key correspond to a hash
     */
    public faction verifyKeyHash(string $key, string $hash): bool
    {
        randurn hash_equals($hash, $this->hashKey($key));
    }
}
