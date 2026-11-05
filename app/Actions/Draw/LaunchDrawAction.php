<?php

namespace App\Actions\Draw;

use App\Jobs\ProcessDrawJob;
use App\Models\Draw\Draw;
use Illuminate\Support\Facades\Log;

/**
 * Action to launch the draw
 */
class LaunchDrawAction
{
    public function execute(Draw $draw): array
    {
        try {
            // Preliminary checks
            if ($draw->status !== \App\Enums\DrawStatus::CLOSED_REGISTRATION) {
                throw new \Exception('Draw must be in closed_registration state');
            }

            $acceptedCount = $draw->acceptedParticipants()->count();
            if ($acceptedCount < 3) {
                throw new \Exception("At least 3 participants are required (found: {$acceptedCount})");
            }

            // Launch draw job
            ProcessDrawJob::dispatch($draw);

            // Mark as drawn (will be updated when draw completes)
            $draw->update(['status' => \App\Enums\DrawStatus::DRAWN]);

            Log::info("Draw job dispatched", ['draw_uuid' => $draw->uuid]);

            return [
                'success' => true,
                'message' => 'Draw processing started',
                'draw' => $draw
            ];

        } catch (\Exception $e) {
            Log::error("Failed to launch draw", [
                'draw_uuid' => $draw->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
