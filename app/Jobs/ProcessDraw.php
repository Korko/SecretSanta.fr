<?php

namespace App\Jobs;


namespace App\Jobs;

use App\Models\Draw\Draw;
use App\Services\Draw\DrawService;
use App\Notifications\DrawCompletedNotification;
use App\Notifications\DrawFailedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Job pour effectuer le tirage au sort
 */
class ProcessDraw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Draw $draw;
    private array $parameters;

    public int $timeout = 300; // 5 minutes
    public int $tries = 3;

    public function __construct(Draw $draw, array $parameters = [])
    {
        $this->draw = $draw;
        $this->parameters = $parameters;
        $this->onQueue('draws');
    }

    public function handle(DrawService $drawService): void
    {
        Log::info("Processing draw job for draw {$this->draw->uuid}");

        try {
            // Effectuer le tirage
            $result = $drawService->performDraw($this->draw);

            if ($result->isSuccessful()) {
                $this->handleSuccessfulDraw($result);
            } else {
                $this->handleFailedDraw($result);
            }

        } catch (\Exception $e) {
            Log::error("Draw processing failed for {$this->draw->uuid}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->handleException($e);
            throw $e; // Re-throw pour que Laravel gère les retry
        }
    }

    /**
     * Gère un tirage réussi
     */
    private function handleSuccessfulDraw($result): void
    {
        Log::info("Draw completed successfully for {$this->draw->uuid}", [
            'duration' => $result->getDuration(),
            'assignments_count' => count($result->getAssignments()),
            'ignored_weak_exclusions' => $result->hasIgnoredWeakExclusions()
        ]);

        // Envoyer les notifications aux participants
        $this->dispatchParticipantNotifications();

        // Notifier l'organisateur
        $this->notifyOrganizer(true, $result->getSummary());
    }

    /**
     * Gère un tirage échoué
     */
    private function handleFailedDraw($result): void
    {
        Log::warning("Draw failed for {$this->draw->uuid}", [
            'errors' => $result->getErrors(),
            'duration' => $result->getDuration()
        ]);

        // Remettre le tirage en état "closed_registration"
        $this->draw->update(['status' => 'closed_registration']);

        // Notifier l'organisateur de l'échec
        $this->notifyOrganizer(false, $result->getFailureReason());
    }

    /**
     * Gère les exceptions
     */
    private function handleException(\Exception $e): void
    {
        // Remettre le tirage en état "closed_registration"
        $this->draw->update(['status' => 'closed_registration']);

        // Notifier l'organisateur
        $this->notifyOrganizer(false, 'An unexpected error occurred: ' . $e->getMessage());
    }

    /**
     * Envoie les notifications à tous les participants
     */
    private function dispatchParticipantNotifications(): void
    {
        $participants = $this->draw->acceptedParticipants;

        foreach ($participants as $participant) {
            SendParticipantDrawNotification::dispatch($participant)
                ->delay(now()->addMinutes(rand(1, 10))); // Étaler sur 10 minutes
        }
    }

    /**
     * Notifie l'organisateur du résultat
     */
    private function notifyOrganizer(bool $success, string $message): void
    {
        $organizerParticipant = $this->draw->participants()
            ->where('is_organizer', true)
            ->first();

        if ($organizerParticipant) {
            if ($success) {
                NotifyOrganizer::dispatch($organizerParticipant, 'draw_completed', [
                    'message' => $message,
                    'draw_uuid' => $this->draw->uuid
                ]);
            } else {
                NotifyOrganizer::dispatch($organizerParticipant, 'draw_failed', [
                    'message' => $message,
                    'draw_uuid' => $this->draw->uuid
                ]);
            }
        }
    }
}
