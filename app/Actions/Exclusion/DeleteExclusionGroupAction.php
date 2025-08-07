<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Exclusion;
use App\Models\Draw\ExclusionGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Action to delete an exclusion group
 */
class DeleteExclusionGroupAction
{
    public function execute(ExclusionGroup $group): array
    {
        DB::beginTransaction();

        try {
            $drawId = $group->draw_id;

            // Delete all exclusions linked to the group
            Exclusion::where('draw_id', $drawId)
                ->where('source', 'group')
                ->whereIn('participant_id', $group->members()->pluck('participant_id'))
                ->delete();

            // Delete the group (members will be deleted in cascade)
            $group->delete();

            DB::commit();

            Log::info("Exclusion group deleted", [
                'group_id' => $group->id,
                'draw_id' => $drawId
            ]);

            return [
                'success' => true,
                'message' => 'exclusion group deleted successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete Exclusion group", [
                'group_id' => $group->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
