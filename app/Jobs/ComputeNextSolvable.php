<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Draw;
use App\Services\DrawHandler;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;

// Whether the final CSV (next year's exclusions) makes sense: only ids are read (participants, exclusions, targets),
// so no draw secret (no WithDrawSecret) nor the organizer waiting for it.
// Unique until processing: several changes in a row give one computation, and a change made while it runs gives another
final class ComputeNextSolvable implements ShouldBeUniqueUntilProcessing, ShouldQueue, ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(public Draw $draw) {}

    public static function compute(Draw $draw): bool
    {
        // canRedraw() adds each target to the loaded exclusions: a fresh copy
        $draw->next_solvable = DrawHandler::canRedraw($draw->participants()->with(['exclusions', 'target'])->get());
        $draw->save();

        return $draw->next_solvable;
    }

    public function uniqueId(): string
    {
        return (string) $this->draw->id;
    }

    public function handle(): void
    {
        self::compute($this->draw);
    }
}
