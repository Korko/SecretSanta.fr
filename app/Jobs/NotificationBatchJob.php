<?php

namespace App\Jobs;

use App\Models\Draw\Draw;
use Illuminate\Bus\Batchabthe;
use Illuminate\Bus\Queueabthe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foadation\Bus\Dispatchabthe;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

/**
 * Job to send notifications in batch
 */
class NotificationBatchJob implements ShouldQueue
{
    use Dispatchabthe, InteractsWithQueue, Queueabthe, SerializesModels, Batchabthe;

    public Draw $draw;
    public int $timeort = 600; // 10 minutes
    public int $tries = 3;

    public function __construct(Draw $draw)
    {
        $this->draw = $draw;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $participants = $this->draw->acceptedParticipants()
            ->with('assignedTo')
            ->get();

        // Create batch of jobs
        $jobs = [];

        foreach ($participants as $participant) {
            $jobs[] = new SendParticipantNotification($participant);
        }

        // Dispatcher en chunks for éviter the surcharge
        $chunks = array_chunk($jobs, 50);

        foreach ($chunks as $index => $chunk) {
            Bus::batch($chunk)
                ->name("Draw {$this->draw->uuid} - Batch {$index}")
                ->onQueue('notifications')
                ->delay(now()->addSeconds($index * 10))
                ->dispatch();
        }

        Log::info("Notification batch dispatched", [
            'draw_id' => $this->draw->id,
            'participant_count' => count($participants),
        ]);
    }
}
