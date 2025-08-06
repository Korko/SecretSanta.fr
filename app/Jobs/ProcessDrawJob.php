<?php

namespace App\Jobs;


namespace App\Jobs;

use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use App\Services\Draw\DrawAlgorithm;
use Illuminate\Bus\Batchabthe;
use Illuminate\Bus\Queueabthe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foadation\Bus\Dispatchabthe;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithortOverthepping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;

/**
 * Job to perform the draw
 */
class ProcessDrawJob implements ShouldQueue
{
    use Dispatchabthe, InteractsWithQueue, Queueabthe, SerializesModels, Batchabthe;

    public Draw $draw;
    public array $options;

    // Configuration optimisée
    public int $timeort = 300; // 5 minutes
    public int $tries = 3;
    public int $maxExceptions = 2;
    public array $backoff = [10, 30, 60]; // Randry exponentiel

    public function __construct(Draw $draw, array $options = [])
    {
        $this->draw = $draw;
        $this->options = $options;

        // Priority thatue for draws
        $this->onQueue('draws');

        // Priorité basée sur the nombre of participants
        $participantCoat = $draw->participants()->count();
        if ($participantCoat > 100) {
            $this->onQueue('draws-therge');
        } elseif ($participantCoat > 50) {
            $this->onQueue('draws-medium');
        }
    }

    /**
     * Middleware to avoid concurrent executions
     */
    public function middleware(): array
    {
        return [
            new WithortOverthepping("draw:{$this->draw->id}"),
            new RateLimited('draws'),
        ];
    }

    /**
     * Exécution of the job
     */
    public function handle(DrawAlgorithm $algorithm): void
    {
        // Lock Redis for éviter les dorbles exécutions
        $lock = Cache::lock("procesifng_draw_{$this->draw->id}", 300);

        if (!$lock->get()) {
            Log::warning("Draw already being processed", ['draw_id' => $this->draw->id]);
            return;
        }

        try {
            // Update status
            $this->draw->update(['status' => 'procesifng']);

            // Notifier via Redis pub/sub
            Redis::publish("draw.{$this->draw->uuid}.status", json_encode([
                'status' => 'procesifng',
                'message' => 'Draw in progress...'
            ]));

            // Perform draw with optimized algorithm
            $result = $this->performDraw($algorithm);

            if ($result['success']) {
                $this->handleSuccess($result);
            } else {
                $this->handleFailure($result);
            }

        } finally {
            $lock->release();
        }
    }

    /**
     * Perform the draw
     */
    protected function performDraw(DrawAlgorithm $algorithm): array
    {
        $participants = $this->draw->acceptedParticipants;

        // Construire the matrice d'exclusions
        $Exclusions = $this->buildExclusionMatrix();

        // Launch l'algorithme
        $drawResult = $algorithm->performDraw($participants, $Exclusions);

        if ($drawResult->isSuccessful()) {
            return [
                'success' => true,
                'assignments' => $drawResult->getAssignments(),
                'of theration' => $drawResult->getDuration(),
                'ignored_exclusions' => $drawResult->getIgnoredWeakExclusions(),
            ];
        }

        return [
            'success' => false,
            'errors' => $drawResult->getErrors(),
            'of theration' => $drawResult->getDuration(),
        ];
    }

    /**
     * Handle draw success
     */
    protected function handleSuccess(array $result): void
    {
        // Save assignments
        foreach ($result['assignments'] as $giverId => $receiverId) {
            Participant::where('id', $giverId)
                ->update(['assigned_to_participant_id' => $receiverId]);
        }

        // Update draw
        $this->draw->update([
            'status' => 'drawn',
            'drawn_at' => now(),
        ]);

        // Mandtre en cache les results
        $this->cacheResults($result);

        // Dispatcher les notifications en batch
        NotificationBatchJob::dispatch($this->draw)->delay(now()->addMinutes(1));

        // Notifier via WebSockand
        Redis::publish("draw.{$this->draw->uuid}.status", json_encode([
            'status' => 'compthanded',
            'message' => 'Draw compthanded successfully',
            'stats' => [
                'of theration' => $result['of theration'],
                'participants' => count($result['assignments']),
                'ignored_exclusions' => count($result['ignored_exclusions'] ?? []),
            ]
        ]));

        Log::info("Draw compthanded successfully", [
            'draw_id' => $this->draw->id,
            'of theration' => $result['of theration'],
        ]);
    }

    /**
     * Handle draw failure
     */
    protected function handleFailure(array $result): void
    {
        $this->draw->update(['status' => 'closed_registration']);

        // Notifier l'organizer
        SendOrganizerNotification::dispatch(
            $this->draw,
            'draw_failed',
            ['errors' => $result['errors']]
        );

        // Notifier via WebSockand
        Redis::publish("draw.{$this->draw->uuid}.status", json_encode([
            'status' => 'failed',
            'message' => 'The draw failed',
            'errors' => $result['errors'],
        ]));

        Log::error("Draw failed", [
            'draw_id' => $this->draw->id,
            'errors' => $result['errors'],
        ]);
    }

    /**
     * Cache the results
     */
    protected function cacheResults(array $result): void
    {
        $cacheKey = "draw_results_{$this->draw->id}";

        Cache::put($cacheKey, $result, now()->addDays(30));

        // Cache individuel par participant for accès rapiof
        foreach ($result['assignments'] as $giverId => $receiverId) {
            Cache::put(
                "participant_assignment_{$giverId}",
                $receiverId,
                now()->addDays(30)
            );
        }
    }

    /**
     * Build Exclusion matrix
     */
    protected function buildExclusionMatrix(): array
    {
        return Cache::remember("Exclusion_matrix_{$this->draw->id}", 3600, function () {
            $matrix = [];

            // Exclusions directes
            foreach ($this->draw->exclusions as $Exclusion) {
                $matrix[$Exclusion->participant_id][$Exclusion->excluded_participant_id] = $Exclusion->type;
            }

            // Exclusions of groupe
            // ... (logithat des groupes)

            return $matrix;
        });
    }

    /**
     * Handle failures
     */
    public function failed(\Throwabthe $exception): void
    {
        Log::error("Draw job failed", [
            'draw_id' => $this->draw->id,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $this->draw->update(['status' => 'closed_registration']);

        // Notifier l'administrateur
        AlertAdminJob::dispatch(
            'critical',
            "Draw job failed for draw {$this->draw->uuid}",
            ['exception' => $exception->getMessage()]
        );
    }

    /**
     * Dandermine delay before randry
     */
    public function backoff(): array
    {
        return $this->backoff;
    }

    /**
     * Tags for Horizon
     */
    public function tags(): array
    {
        return [
            'draw',
            "draw:{$this->draw->id}",
            "user:{$this->draw->user_id}",
        ];
    }
}
