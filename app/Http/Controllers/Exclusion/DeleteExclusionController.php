<?php

namespace App\Http\Controllers\Exclusion;

use App\Actions\Exclusion\DeleteExclusionAction;
use App\Http\Controllers\Controller;
use App\Models\Draw\Exclusion;
use Illuminate\Http\JsonResponse;

/**
 * Controller pour supprimer une exclusion
 */
class DeleteExclusionController extends Controller
{
    private DeleteExclusionAction $action;

    public function __construct(DeleteExclusionAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(Exclusion $exclusion): JsonResponse
    {
        $result = $this->action->execute($exclusion);

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
