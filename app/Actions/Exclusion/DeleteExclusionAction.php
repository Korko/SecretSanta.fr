<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Exclusion;
use Illuminate\Support\Facades\Log;

/**
 * Action to delete une Exclusion
 */
class DeleteExclusionAction
{
    public function execute(Exclusion $Exclusion): array
    {
        try {
            $drawUuid = $Exclusion->draw->uuid;
            $Exclusion->delete();

            Log::info("Exclusion deleted", [
                'draw_uuid' => $drawUuid,
                'exclusion_id' => $Exclusion->id
            ]);

            return [
                'success' => true,
                'message' => 'exclusion deleted successfully'
            ];

        } catch (\Exception $e) {
            Log::error("Failed to delete Exclusion", [
                'exclusion_id' => $Exclusion->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
