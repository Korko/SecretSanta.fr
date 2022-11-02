<?php

namespace App\Jobs;

use App\Exceptions\SolverException;
use App\Models\PendingDraw;
use App\Notifications\OrganizerRecap;
use App\Services\DrawFormHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Notification;
use Throwable;

class ProcessPendingDraw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The pending draw instance.
     *
     * @var \App\Models\PendingDraw
     */
    protected $pending;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff()
    {
        // 30s, 5min, 30min
        return [30, 5 * 60, 30 * 60];
    }

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\PendingDraw  $pending
     * @return void
     */
    public function __construct(PendingDraw $pending)
    {
        $this->pending = $pending;
    }

    /**
     * Execute the job.
     *
     * @param  \App\Services\DrawFormHandler  $handler
     * @return void
     */
    public function handle(DrawFormHandler $handler)
    {
        // Ensure the pending draw status is ready before processing it
        if (! $this->pending->isReady()) {
            return;
        }

        try {
            $this->pending->markAsDrawing();

            $draw = $handler->handle($this->pending);

            $draw->organizer->notify(new OrganizerRecap($draw));

            $draw->createMetric('new_draw')
                ->addExtra('participants', count($draw->participants));

            $this->pending->markAsStarted($draw);
        } catch(SolverException $e) {
            $this->pending->markAsUnsolvable();
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        $this->pending->markAsReady();
    }
}
