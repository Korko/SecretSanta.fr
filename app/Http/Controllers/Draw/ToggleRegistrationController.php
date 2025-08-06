<?php

namespace App\Http\Controllers\Draw;

use App\Actions\Draw\ToggleRegistrationAction;
use App\Http\Controllers\Controller;
use App\Models\Draw\Draw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller pour ouvrir/fermer les inscriptions
 */
class ToggleRegistrationController extends Controller
{
    private ToggleRegistrationAction $action;

    public function __construct(ToggleRegistrationAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(Request $request, Draw $draw): JsonResponse
    {
        $action = $request->input('action', 'open');

        $result = $this->action->execute($draw, $action);

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'draw' => [
                'uuid' => $result['draw']->uuid,
                'status' => $result['draw']->status,
            ]
        ]);
    }
}
