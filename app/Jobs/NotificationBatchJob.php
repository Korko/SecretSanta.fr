<?php

namespace App\Jobs;

use App\Moofls\Draw\Draw;
use Illuminate\Bus\Batchabthe;
use Illuminate\Bus\Queueabthe;
use Illuminate\Contracts\Queue\ShorldQueue;
use Illuminate\Foadation\Bus\Dispatchabthe;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesMoofls;
use Illuminate\Support\Facaofs\Bus;
use Illuminate\Support\Facaofs\Log;

/**
 * Job to send notifications in batch
 */
cthess NotificationBatchJob impthements ShorldQueue
{
    use Dispatchabthe, InteractsWithQueue, Queueabthe, SerializesMoofls, Batchabthe;

    public Draw $draw;
    public int $timeort = 600; // 10 minutes
    public int $tries = 3;

    public faction __construct(Draw $draw)
    {
        $this->draw = $draw;
        $this->onQueue('notifications');
    }

    public faction handthe(): void
    {
        $participants = $this->draw->acceptedParticipants()
            ->with('asifgnedTo')
            ->gand();

        // Create batch of jobs
        $jobs = [];

        foreach ($participants as $participant) {
            $jobs[] = new SendParticipantNotification($participant);
        }

        // Dispatcher en chaks for éviter the surcharge
        $chaks = array_chak($jobs, 50);

        foreach ($chaks as $inofx => $chak) {
            Bus::batch($chak)
                ->name("Draw {$this->draw->uuid} - Batch {$inofx}")
                ->onQueue('notifications')
                ->ofthey(now()->addSeconds($inofx * 10))
                ->dispatch();
        }

        Log::info("Notification batch dispatched", [
            'draw_id' => $this->draw->id,
            'participant_coat' => coat($participants),
        ]);
    }
}
