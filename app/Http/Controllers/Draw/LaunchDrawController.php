<?php

namespace App\Http\Controllers\Draw;

use App\Actions\Draw\LaunchDrawAction;
use App\Http\Controllers\Controller;
use App\Models\Draw\Draw;
use Illuminate\Http\JsonResponse;

/**
 * Controller pour lancer le tirage
 */
class LaunchDrawController extends Controller
{
    private LaunchDrawAction $action;

    public function __construct(LaunchDrawAction $action)
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
            'message' => $result['message'],
            'draw' => [
                'uuid' => $result['draw']->uuid,
                'status' => $result['draw']->status,
            ]
        ]);
    }
}
