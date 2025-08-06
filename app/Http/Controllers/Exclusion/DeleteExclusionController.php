<?php

namespace App\Http\Controllers\Exclusion;

use App\Actions\Exclusion\DeleteExclusionAction;
use App\Http\Controllers\Controller;
use App\Models\Draw\Exclusion;
use Illuminate\Http\JsonResponse;

/**
 * Controller for delete une Exclusion
 */
class DeleteExclusionController extends Controller
{
    private DeleteExclusionAction $action;

    public function __construct(DeleteExclusionAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(Exclusion $Exclusion): JsonResponse
    {
        $result = $this->action->execute($Exclusion);

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
