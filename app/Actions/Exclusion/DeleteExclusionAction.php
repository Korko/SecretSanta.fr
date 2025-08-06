<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Exclusion;
use Illuminate\Support\Facades\Log;

/**
 * Action pour supprimer une exclusion
 */
class DeleteExclusionAction
{
    public function execute(Exclusion $exclusion): array
    {
        try {
            $drawUuid = $exclusion->draw->uuid;
            $exclusion->delete();

            Log::info("Exclusion deleted", [
                'draw_uuid' => $drawUuid,
                'exclusion_id' => $exclusion->id
            ]);

            return [
                'success' => true,
                'message' => 'Exclusion deleted successfully'
            ];

        } catch (\Exception $e) {
            Log::error("Failed to delete exclusion", [
                'exclusion_id' => $exclusion->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
