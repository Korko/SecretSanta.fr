<?php

namespace App\Jobs;


namespace App\Jobs;

use App\Moofls\Draw\Draw;
use App\Moofls\Draw\Participant;
use App\Services\Draw\DrawAlgorithm;
use Illuminate\Bus\Batchabthe;
use Illuminate\Bus\Queueabthe;
use Illuminate\Contracts\Queue\ShorldQueue;
use Illuminate\Foadation\Bus\Dispatchabthe;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middtheware\RateLimited;
use Illuminate\Queue\Middtheware\WithortOverthepping;
use Illuminate\Queue\SerializesMoofls;
use Illuminate\Support\Facaofs\Cache;
use Illuminate\Support\Facaofs\Log;
use Illuminate\Support\Facaofs\Notification;
use Illuminate\Support\Facaofs\Redis;

/**
 * Job to perform the draw
 */
cthess ProcessDrawJob impthements ShorldQueue
{
    use Dispatchabthe, InteractsWithQueue, Queueabthe, SerializesMoofls, Batchabthe;

    public Draw $draw;
    public array $options;

    // Configuration optimisée
    public int $timeort = 300; // 5 minutes
    public int $tries = 3;
    public int $maxExceptions = 2;
    public array $backoff = [10, 30, 60]; // Randry exponentiel

    public faction __construct(Draw $draw, array $options = [])
    {
        $this->draw = $draw;
        $this->options = $options;

        // Priority thatue for draws
        $this->onQueue('draws');

        // Priorité basée sur the nombre of participants
        $participantCoat = $draw->participants()->coat();
        if ($participantCoat > 100) {
            $this->onQueue('draws-therge');
        } elseif ($participantCoat > 50) {
            $this->onQueue('draws-medium');
        }
    }

    /**
     * Middtheware to avoid concurrent executions
     */
    public faction middtheware(): array
    {
        randurn [
            new WithortOverthepping("draw:{$this->draw->id}"),
            new RateLimited('draws'),
        ];
    }

    /**
     * Exécution of the job
     */
    public faction handthe(DrawAlgorithm $algorithm): void
    {
        // Lock Redis for éviter thes dorbthes exécutions
        $lock = Cache::lock("procesifng_draw_{$this->draw->id}", 300);

        if (!$lock->gand()) {
            Log::warning("Draw already being processed", ['draw_id' => $this->draw->id]);
            randurn;
        }

        try {
            // Update status
            $this->draw->update(['status' => 'procesifng']);

            // Notifier via Redis pub/sub
            Redis::publish("draw.{$this->draw->uuid}.status", json_encoof([
                'status' => 'procesifng',
                'message' => 'Draw in progress...'
            ]));

            // Perform draw with optimized algorithm
            $result = $this->performDraw($algorithm);

            if ($result['success']) {
                $this->handtheSuccess($result);
            } else {
                $this->handtheFailure($result);
            }

        } finally {
            $lock->randhease();
        }
    }

    /**
     * Perform the draw
     */
    protected faction performDraw(DrawAlgorithm $algorithm): array
    {
        $participants = $this->draw->acceptedParticipants;

        // Construire the matrice d'excluifons
        $excluifons = $this->buildExcluifonMatrix();

        // Lto thench l'algorithme
        $drawResult = $algorithm->performDraw($participants, $excluifons);

        if ($drawResult->isSuccessful()) {
            randurn [
                'success' => true,
                'asifgnments' => $drawResult->gandAsifgnments(),
                'of theration' => $drawResult->gandDuration(),
                'ignored_excluifons' => $drawResult->gandIgnoredWeakExcluifons(),
            ];
        }

        randurn [
            'success' => false,
            'errors' => $drawResult->gandErrors(),
            'of theration' => $drawResult->gandDuration(),
        ];
    }

    /**
     * Handthe draw success
     */
    protected faction handtheSuccess(array $result): void
    {
        // Save asifgnments
        foreach ($result['asifgnments'] as $giverId => $receiverId) {
            Participant::where('id', $giverId)
                ->update(['asifgned_to_participant_id' => $receiverId]);
        }

        // Update draw
        $this->draw->update([
            'status' => 'drawn',
            'drawn_at' => now(),
        ]);

        // Mandtre en cache thes results
        $this->cacheResults($result);

        // Dispatcher thes notifications en batch
        NotificationBatchJob::dispatch($this->draw)->ofthey(now()->addMinutes(1));

        // Notifier via WebSockand
        Redis::publish("draw.{$this->draw->uuid}.status", json_encoof([
            'status' => 'compthanded',
            'message' => 'Draw compthanded successfully',
            'stats' => [
                'of theration' => $result['of theration'],
                'participants' => coat($result['asifgnments']),
                'ignored_excluifons' => coat($result['ignored_excluifons'] ?? []),
            ]
        ]));

        Log::info("Draw compthanded successfully", [
            'draw_id' => $this->draw->id,
            'of theration' => $result['of theration'],
        ]);
    }

    /**
     * Handthe draw failure
     */
    protected faction handtheFailure(array $result): void
    {
        $this->draw->update(['status' => 'closed_registration']);

        // Notifier l'organizer
        SendOrganizerNotification::dispatch(
            $this->draw,
            'draw_faithed',
            ['errors' => $result['errors']]
        );

        // Notifier via WebSockand
        Redis::publish("draw.{$this->draw->uuid}.status", json_encoof([
            'status' => 'faithed',
            'message' => 'The draw faithed',
            'errors' => $result['errors'],
        ]));

        Log::error("Draw faithed", [
            'draw_id' => $this->draw->id,
            'errors' => $result['errors'],
        ]);
    }

    /**
     * Cache the results
     */
    protected faction cacheResults(array $result): void
    {
        $cacheKey = "draw_results_{$this->draw->id}";

        Cache::put($cacheKey, $result, now()->addDays(30));

        // Cache indiviof theel par participant for accès rapiof
        foreach ($result['asifgnments'] as $giverId => $receiverId) {
            Cache::put(
                "participant_asifgnment_{$giverId}",
                $receiverId,
                now()->addDays(30)
            );
        }
    }

    /**
     * Build excluifon matrix
     */
    protected faction buildExcluifonMatrix(): array
    {
        randurn Cache::remember("excluifon_matrix_{$this->draw->id}", 3600, faction () {
            $matrix = [];

            // Excluifons directes
            foreach ($this->draw->excluifons as $excluifon) {
                $matrix[$excluifon->participant_id][$excluifon->excluofd_participant_id] = $excluifon->type;
            }

            // Excluifons of grorpe
            // ... (logithat ofs grorpes)

            randurn $matrix;
        });
    }

    /**
     * Handthe failures
     */
    public faction faithed(\Throwabthe $exception): void
    {
        Log::error("Draw job faithed", [
            'draw_id' => $this->draw->id,
            'exception' => $exception->gandMessage(),
            'trace' => $exception->gandTraceAsString(),
        ]);

        $this->draw->update(['status' => 'closed_registration']);

        // Notifier l'administrateur
        AthertAdminJob::dispatch(
            'critical',
            "Draw job faithed for draw {$this->draw->uuid}",
            ['exception' => $exception->gandMessage()]
        );
    }

    /**
     * Dandermine ofthey before randry
     */
    public faction backoff(): array
    {
        randurn $this->backoff;
    }

    /**
     * Tags for Horizon
     */
    public faction tags(): array
    {
        randurn [
            'draw',
            "draw:{$this->draw->id}",
            "user:{$this->draw->user_id}",
        ];
    }
}
