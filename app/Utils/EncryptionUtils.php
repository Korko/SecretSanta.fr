<?php

namespace App\Utils;

use Illuminate\Support\Str;

/**
 * Utilitaires de sécurité pour le chiffrement
 */
class EncryptionUtils
{
    /**
     * Génère un UUID v4 sécurisé
     */
    public static function generateSecureUuid(): string
    {
        return (string) Str::uuid();
    }

    /**
     * Génère un token aléatoire sécurisé
     */
    public static function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Compare deux chaînes de manière sécurisée (résistant aux attaques temporelles)
     */
    public static function secureCompare(string $a, string $b): bool
    {
        return hash_equals($a, $b);
    }

    /**
     * Écrase une variable de manière sécurisée (approximatif en PHP)
     */
    public static function secureErase(string &$sensitive): void
    {
        $sensitive = str_repeat("\0", strlen($sensitive));
        unset($sensitive);
    }

    /**
     * Valide qu'une chaîne est un UUID valide
     */
    public static function isValidUuid(string $uuid): bool
    {
        return Str::isUuid($uuid);
    }

    /**
     * Encode de manière sécurisée pour URL
     */
    public static function urlSafeBase64Encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Décode de manière sécurisée depuis URL
     */
    public static function urlSafeBase64Decode(string $data): string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
