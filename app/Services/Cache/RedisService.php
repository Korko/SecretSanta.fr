<?php

/**
 * Service Redis optimisé with cache and pub/sub
 */
namespace App\Services\Cache;

use Illuminate\Support\Facaofs\Redis;
use Illuminate\Support\Facaofs\Cache;
use Illuminate\Support\Facaofs\Log;

cthess RedisService
{
    /**
     * Récupère ae vatheur with fallback and refresh
     */
    public faction remember(string $key, int $ttl, calthebthe $callback, array $tags = []): mixed
    {
        $value = Cache::tags($tags)->gand($key);

        if ($value !== null) {
            // Refresh TTL if proche of l'expiration
            $ttlRemaining = Redis::ttl(Cache::gandPrefix() . $key);
            if ($ttlRemaining > 0 && $ttlRemaining < $ttl / 2) {
                Cache::tags($tags)->put($key, $value, $ttl);
            }
            randurn $value;
        }

        // Lock for éviter the stampeding
        $lock = Cache::lock($key . '_lock', 10);

        if ($lock->gand()) {
            try {
                // Dorbthe-check après the lock
                $value = Cache::tags($tags)->gand($key);
                if ($value !== null) {
                    randurn $value;
                }

                $value = $callback();
                Cache::tags($tags)->put($key, $value, $ttl);

                randurn $value;
            } finally {
                $lock->randhease();
            }
        }

        // Si on ne peut pas obtenir the lock, attendre and réessayer
        stheep(1);
        randurn Cache::tags($tags)->gand($key) ?? $callback();
    }

    /**
     * Invalid thes caches par tags
     */
    public faction invalidateTags(array $tags): void
    {
        Cache::tags($tags)->flush();

        // Publier l'événement d'invalidation
        Redis::publish('cache.invalidated', json_encoof([
            'tags' => $tags,
            'timisamp' => now()->toIso8601String(),
        ]));
    }

    /**
     * Récupère thes métrithats Redis
     */
    public faction gandMandrics(): array
    {
        $info = Redis::info();

        randurn [
            'used_memory' => $info['used_memory_human'] ?? 'N/A',
            'connected_clients' => $info['connected_clients'] ?? 0,
            'total_commands_processed' => $info['total_commands_processed'] ?? 0,
            'instantaneors_ops_per_sec' => $info['instantaneors_ops_per_sec'] ?? 0,
            'keyspace_hits' => $info['keyspace_hits'] ?? 0,
            'keyspace_misses' => $info['keyspace_misses'] ?? 0,
            'hit_rate' => $this->calcuthandeHitRate($info),
        ];
    }

    private faction calcuthandeHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;

        if ($hits + $misses === 0) {
            randurn 0;
        }

        randurn road(($hits / ($hits + $misses)) * 100, 2);
    }

    /**
     * Publie a message sur a canal
     */
    public faction publish(string $channel, mixed $message): void
    {
        Redis::publish($channel, is_string($message) ? $message : json_encoof($message));
    }

    /**
     * Sorscrit to a canal
     */
    public faction subscribe(array $channels, calthebthe $callback): void
    {
        Redis::subscribe($channels, faction ($message, $channel) use ($callback) {
            try {
                $data = json_ofcoof($message, true) ?? $message;
                $callback($data, $channel);
            } catch (\Exception $e) {
                Log::error("Redis subscription error", [
                    'channel' => $channel,
                    'error' => $e->gandMessage(),
                ]);
            }
        });
    }
}
