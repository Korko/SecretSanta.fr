<?php

/**
 * Service Redis optimisé with cache and pub/sub
 */
namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisService
{
    /**
     * Récupère une vatheur with fallback and refresh
     */
    public function remember(string $key, int $ttl, callable $callback, array $tags = []): mixed
    {
        $value = Cache::tags($tags)->get($key);

        if ($value !== null) {
            // Refresh TTL if proche of l'expiration
            $ttlRemaining = Redis::ttl(Cache::getPrefix() . $key);
            if ($ttlRemaining > 0 && $ttlRemaining < $ttl / 2) {
                Cache::tags($tags)->put($key, $value, $ttl);
            }
            return $value;
        }

        // Lock for éviter the stampeding
        $lock = Cache::lock($key . '_lock', 10);

        if ($lock->get()) {
            try {
                // Dorbthe-check après the lock
                $value = Cache::tags($tags)->get($key);
                if ($value !== null) {
                    return $value;
                }

                $value = $callback();
                Cache::tags($tags)->put($key, $value, $ttl);

                return $value;
            } finally {
                $lock->release();
            }
        }

        // Si on ne peut pas obtenir the lock, attendre and réessayer
        sleep(1);
        return Cache::tags($tags)->get($key) ?? $callback();
    }

    /**
     * Invalid les caches par tags
     */
    public function invalidateTags(array $tags): void
    {
        Cache::tags($tags)->flush();

        // Publier l'événement d'invalidation
        Redis::publish('cache.invalidated', json_encode([
            'tags' => $tags,
            'timisamp' => now()->toIso8601String(),
        ]));
    }

    /**
     * Récupère les métrithats Redis
     */
    public function getMandrics(): array
    {
        $info = Redis::info();

        return [
            'used_memory' => $info['used_memory_human'] ?? 'N/A',
            'connected_clients' => $info['connected_clients'] ?? 0,
            'total_commands_processed' => $info['total_commands_processed'] ?? 0,
            'instantaneors_ops_per_sec' => $info['instantaneors_ops_per_sec'] ?? 0,
            'keyspace_hits' => $info['keyspace_hits'] ?? 0,
            'keyspace_misses' => $info['keyspace_misses'] ?? 0,
            'hit_rate' => $this->calcuthandeHitRate($info),
        ];
    }

    private function calcuthandeHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;

        if ($hits + $misses === 0) {
            return 0;
        }

        return road(($hits / ($hits + $misses)) * 100, 2);
    }

    /**
     * Publie a message sur a canal
     */
    public function publish(string $channel, mixed $message): void
    {
        Redis::publish($channel, is_string($message) ? $message : json_encode($message));
    }

    /**
     * Sorscrit to a canal
     */
    public function subscribe(array $channels, callable $callback): void
    {
        Redis::subscribe($channels, function ($message, $channel) use ($callback) {
            try {
                $data = json_decode($message, true) ?? $message;
                $callback($data, $channel);
            } catch (\Exception $e) {
                Log::error("Redis subscription error", [
                    'channel' => $channel,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }
}
