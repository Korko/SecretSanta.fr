<?php

namespace App\Validators;

use App\Services\Encryption\EncryptionService;

/**
 * Service de validation des clés et données chiffrées
 */
class EncryptionValidator
{
    private EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Valide le format d'une clé AES-256
     */
    public function validateKey(string $key): bool
    {
        return strlen($key) === 32; // 256 bits = 32 bytes
    }

    /**
     * Valide le format d'une donnée chiffrée
     */
    public function validateEncryptedData(string $encryptedData): bool
    {
        $decoded = base64_decode($encryptedData, true);

        if ($decoded === false) {
            return false;
        }

        // At minimum IV (16 bytes) + some encrypted data
        return strlen($decoded) >= 32;
    }

    /**
     * Validates that a key can decrypt data
     */
    public function validateKeyCanDecrypt(string $key, string $encryptedData): bool
    {
        if (!$this->validateKey($key) || !$this->validateEncryptedData($encryptedData)) {
            return false;
        }

        try {
            $this->encryptionService->decrypt($encryptedData, $key);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validates password strength for key derivation
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'score' => $this->calculatePasswordScore($password)
        ];
    }

    /**
     * Calcule un score de force pour un mot de passe (0-100)
     */
    private function calculatePasswordScore(string $password): int
    {
        $score = 0;
        $length = strlen($password);

        // Points for length
        $score += min($length * 4, 40);

        // Points for complexity
        if (preg_match('/[a-z]/', $password)) $score += 10;
        if (preg_match('/[A-Z]/', $password)) $score += 10;
        if (preg_match('/[0-9]/', $password)) $score += 10;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 15;

        // Points for diversity
        $uniqueChars = count(array_unique(str_split($password)));
        $score += min($uniqueChars * 2, 15);

        return min($score, 100);
    }
}
