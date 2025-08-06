<?php

namespace App\Services\Encryption;

/**
 * Service principal de gestion du chiffrement à deux niveaux
 * - Clé Master : chiffre toutes les données du tirage
 * - Clés individuelles : chiffrent la clé master pour chaque participant
 */
class EncryptionService
{
    private const AES_METHOD = 'AES-256-CBC';
    private const KEY_LENGTH = 32; // 256 bits
    private const IV_LENGTH = 16;  // 128 bits

    /**
     * Génère une nouvelle clé AES-256 aléatoire
     */
    public function generateKey(): string
    {
        return random_bytes(self::KEY_LENGTH);
    }

    /**
     * Génère une clé dérivée d'un mot de passe avec PBKDF2
     */
    public function deriveKeyFromPassword(string $password, string $salt, int $iterations = 10000): string
    {
        return hash_pbkdf2('sha256', $password, $salt, $iterations, self::KEY_LENGTH, true);
    }

    /**
     * Chiffre des données avec une clé donnée
     */
    public function encrypt(string $data, string $key): string
    {
        $iv = random_bytes(self::IV_LENGTH);
        $encrypted = openssl_encrypt($data, self::AES_METHOD, $key, OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {
            throw new \Exception('Encryption failed');
        }

        // Retourne IV + données chiffrées en base64
        return base64_encode($iv . $encrypted);
    }

    /**
     * Déchiffre des données avec une clé donnée
     */
    public function decrypt(string $encryptedData, string $key): string
    {
        $data = base64_decode($encryptedData);

        if ($data === false || strlen($data) < self::IV_LENGTH) {
            throw new \Exception('Invalid encrypted data');
        }

        $iv = substr($data, 0, self::IV_LENGTH);
        $encrypted = substr($data, self::IV_LENGTH);

        $decrypted = openssl_decrypt($encrypted, self::AES_METHOD, $key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {
            throw new \Exception('Decryption failed');
        }

        return $decrypted;
    }

    /**
     * Crée un hash sécurisé d'une clé pour stockage/identification
     */
    public function hashKey(string $key): string
    {
        return hash('sha256', $key);
    }

    /**
     * Vérifie qu'une clé correspond à un hash
     */
    public function verifyKeyHash(string $key, string $hash): bool
    {
        return hash_equals($hash, $this->hashKey($key));
    }
}
