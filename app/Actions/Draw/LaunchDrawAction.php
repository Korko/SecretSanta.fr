<?php

namespace App\Actions\Draw;

use App\Jobs\ProcessDraw;
use App\Models\Draw\Draw;
use Illuminate\Support\Facades\Log;

/**
 * Action pour lancer le tirage au sort
 */
class LaunchDrawAction
{
    public function execute(Draw $draw): array
    {
        try {
            // Vérifications préalables
            if (!in_array($draw->status, ['closed_registration'])) {
                throw new \Exception('Draw must be in closed_registration state');
            }

            $acceptedCount = $draw->acceptedParticipants()->count();
            if ($acceptedCount < 3) {
                throw new \Exception("At least 3 participants are required (found: {$acceptedCount})");
            }

            // Lancer le job de tirage
            ProcessDraw::dispatch($draw);

            // Marquer comme en cours de traitement
            $draw->update(['status' => 'processing']);

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
