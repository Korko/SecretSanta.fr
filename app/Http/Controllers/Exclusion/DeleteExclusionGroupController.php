<?php

namespace App\Http\Controllers\Exclusion;

use App\Actions\Exclusion\DeleteExclusionGroupAction;
use App\Http\Controllers\Controller;
use App\Models\Draw\ExclusionGroup;
use Illuminate\Http\JsonResponse;

/**
 * Controller pour supprimer un groupe d'exclusion
 */
class DeleteExclusionGroupController extends Controller
{
    private DeleteExclusionGroupAction $action;

    public function __construct(DeleteExclusionGroupAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(ExclusionGroup $group): JsonResponse
    {
        $result = $this->action->execute($group);

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'message' => $result['message']
        ]);
    }
}
