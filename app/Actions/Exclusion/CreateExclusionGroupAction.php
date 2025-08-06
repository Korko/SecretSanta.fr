<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Draw;
use App\Models\Draw\ExclusionGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Action to create a groupe d'exclusion
 */
class CreateExclusionGroupAction
{
    public function execute(Draw $draw, string $name, string $masterKey): array
    {
        DB::beginTransaction();

        try {
            // Create the groupe
            $group = new ExclusionGroup();
            $group->draw_id = $draw->id;
            $group->setEncryptedAttribute('name_encrypted', $name, $masterKey);
            $group->save();

            DB::commit();

            Log::info("Exclusion group created", [
                'draw_uuid' => $draw->uuid,
                'group_id' => $group->id
            ]);

            return [
                'success' => true,
                'message' => 'exclusion group created successfully',
                'group' => $group
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create Exclusion group", [
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
