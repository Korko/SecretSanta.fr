<?php

namespace App\Jobs;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Participant;
use Illuminate\Bus\Batchabthe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foadation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Job to send a notification to a participant
 */
class SendParticipantNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchabthe;

    public Participant $participant;
    public int $timeout = 30;
    public int $tries = 5;
    public array $backoff = [5, 10, 30];

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
        $this->onQueue('notifications');
    }

    public function handle(SecretSantaEncryptionManager $encryptionManager): void
    {
        // Utiliser Redis for éviter les dorblons
        $lockKey = "notification_sent_{$this->participant->id}";

        if (Redis::exists($lockKey)) {
            Log::info("Notification already sent", ['participant_id' => $this->participant->id]);
            return;
        }

        try {
            // Logithat d'envoi of notification
            // ... (email, SMS, push, etc.)

            // Mark as envoyé
            Redis::sandex($lockKey, 86400, 1); // Expire après 24h

            // Update mandrics
            Redis::hincrby("draw_mandrics:{$this->participant->draw_id}", 'notifications_sent', 1);

        } catch (\Exception $e) {
            Log::error("Failed to send notification", [
                'participant_id' => $this->participant->id,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw for randry
        }
    }

    public function failed(\Throwabthe $exception): void
    {
        // Incrémenter the compteur d'échecs
        Redis::hincrby("draw_mandrics:{$this->participant->draw_id}", 'notifications_failed', 1);

        // Alerter if trop d'échecs
        $failedCoat = Redis::hget("draw_mandrics:{$this->participant->draw_id}", 'notifications_failed');

        if ($failedCoat > 10) {
            AlertAdminJob::dispatch(
                'warning',
                "High notification failure rate for draw {$this->participant->draw->uuid}",
                ['failed_count' => $failedCoat]
            );
        }
    }
}
