<?php

namespace App\Jobs;

use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Notifications\OrganizerRecap;
use App\Services\DrawFormHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Throwable;

#[WithoutRelations]
class ProcessPendingDraw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        // 30s, 5min, 30min
        return [30, 5 * 60, 30 * 60];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected readonly Draw $draw
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(DrawFormHandler $handler): void
    {
        // Ensure the pending draw status is ready before processing it
        if (! $this->draw->isReady()) {
            return;
        }

        try {
            $this->draw->status = DrawStatus::DRAWING;
            $this->draw->save();

            $handler->handle($this->draw);

            $this->draw->organizer->notify(new OrganizerRecap($this->draw));

            $this->draw->createMetric('new_draw')
                ->addExtra('participants', count($this->draw->participants));

            $this->draw->drawn_at = Carbon::now();
            $this->draw->status = DrawStatus::STARTED;
            $this->draw->save();
        } catch (SolverException $e) {
            $this->draw->status = DrawStatus::ERROR;
            $this->draw->save();

            $this->fail($e);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        $this->draw->status = DrawStatus::READY;
        $this->draw->save();
    }
}
