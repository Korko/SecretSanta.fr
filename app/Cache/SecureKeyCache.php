<?php

namespace App\Cache;

/**
 * Secure cache for temporary keys (memory only)
 */
class SecureKeyCache
{
    private array $cache = [];
    private int $maxLifetime = 3600; // 1 hour by default

    /**
     * Stores a key temporarily
     */
    public function store(string $identifier, string $key, ?int $lifetime = null): void
    {
        $this->cache[$identifier] = [
            'key' => $key,
            'expires_at' => time() + ($lifetime ?? $this->maxLifetime)
        ];
    }

    /**
     * Retrieves a key from cache
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
     * Secure destructor
     */
    public function __destruct()
    {
        // Secure memory overwrite (approximate in PHP)
        foreach ($this->cache as &$entry) {
            if (isset($entry['key'])) {
                $entry['key'] = str_repeat("\0", strlen($entry['key']));
            }
        }
        $this->cache = [];
    }
}
