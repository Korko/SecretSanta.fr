<?php

namespace App\Jobs;

use App\Cache\SecureKeyCache;
use App\Models\Draw\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job pour nettoyer les données expirées
 */
class CleanupExpiredData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct()
    {
        $this->onQueue('cleanup');
    }

    public function handle(): void
    {
        try {
            $cleanedCount = 0;

            // Nettoyer les tirages archivés anciens (plus de 2 ans)
            $oldDraws = Draw::where('status', 'archived')
                ->where('updated_at', '<', now()->subYears(2))
                ->get();

            foreach ($oldDraws as $draw) {
                $draw->delete(); // Cascade delete via foreign keys
                $cleanedCount++;
            }

            // Nettoyer les jobs échoués anciens (plus de 30 jours)
            \DB::table('failed_jobs')
                ->where('failed_at', '<', now()->subDays(30))
                ->delete();

            // Nettoyer le cache des clés expirées
            if (app()->bound(SecureKeyCache::class)) {
                app(SecureKeyCache::class)->cleanup();
            }

            Log::info("Cleanup completed", [
                'draws_deleted' => $cleanedCount,
                'completed_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error("Cleanup failed", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
