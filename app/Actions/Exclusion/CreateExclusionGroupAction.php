<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Draw;
use App\Models\Draw\ExclusionGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Action pour créer un groupe d'exclusion
 */
class CreateExclusionGroupAction
{
    public function execute(Draw $draw, string $name, string $masterKey): array
    {
        DB::beginTransaction();

        try {
            // Créer le groupe
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
                'message' => 'Exclusion group created successfully',
                'group' => $group
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create exclusion group", [
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
