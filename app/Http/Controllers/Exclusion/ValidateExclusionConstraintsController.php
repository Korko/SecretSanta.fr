<?php

namespace App\Http\Controllers\Exclusion;

use App\Actions\Exclusion\ValidateExclusionConstraintsAction;
use App\Http\Controllers\Controller;
use App\Models\Draw\Draw;
use Illuminate\Http\JsonResponse;

/**
 * Controller for validate les contraintes d'exclusion
 */
class ValidateExclusionConstraintsController extends Controller
{
    private ValidateExclusionConstraintsAction $action;

    public function __construct(ValidateExclusionConstraintsAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(Draw $draw): JsonResponse
    {
        $result = $this->action->execute($draw);

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'valid' => $result['valid'],
            'errors' => $result['errors'],
            'warnings' => $result['warnings']
        ]);
    }
}
