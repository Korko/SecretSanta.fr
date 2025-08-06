<?php

namespace App\Cache;

/**
 * Cache sécurisé pour les clés temporaires (en mémoire uniquement)
 */
class SecureKeyCache
{
    private array $cache = [];
    private int $maxLifetime = 3600; // 1 heure par défaut

    /**
     * Stocke une clé temporairement
     */
    public function store(string $identifier, string $key, ?int $lifetime = null): void
    {
        $this->cache[$identifier] = [
            'key' => $key,
            'expires_at' => time() + ($lifetime ?? $this->maxLifetime)
        ];
    }

    /**
     * Récupère une clé du cache
     */
    public function get(string $identifier): ?string
    {
        if (!isset($this->cache[$identifier])) {
            return null;
        }

        $entry = $this->cache[$identifier];

        if (time() > $entry['expires_at']) {
            unset($this->cache[$identifier]);
            return null;
        }

        return $entry['key'];
    }

    /**
     * Supprime une clé du cache
     */
    public function forget(string $identifier): void
    {
        unset($this->cache[$identifier]);
    }

    /**
     * Nettoie les clés expirées
     */
    public function cleanup(): void
    {
        $now = time();

        foreach ($this->cache as $identifier => $entry) {
            if ($now > $entry['expires_at']) {
                unset($this->cache[$identifier]);
            }
        }
    }

    /**
     * Vide complètement le cache
     */
    public function flush(): void
    {
        $this->cache = [];
    }

    /**
     * Destructeur sécurisé
     */
    public function __destruct()
    {
        // Écrasement sécurisé de la mémoire (approximatif en PHP)
        foreach ($this->cache as &$entry) {
            if (isset($entry['key'])) {
                $entry['key'] = str_repeat("\0", strlen($entry['key']));
            }
        }
        $this->cache = [];
    }
}
