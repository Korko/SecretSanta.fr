<?php

namespace App\Http\Controllers\Draw;

use App\Actions\Draw\CreateDrawAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Draw\CreateDrawRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controller pour créer un tirage
 */
class CreateDrawController extends Controller
{
    private CreateDrawAction $action;

    public function __construct(CreateDrawAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(CreateDrawRequest $request): JsonResponse
    {
        $result = $this->action->execute(
            $request->validated(),
            $request->user()
        );

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'draw' => [
                'uuid' => $result['draw']->uuid,
                'status' => $result['draw']->status,
            ],
            'organizer_link' => $result['organizer_link'],
            'master_key' => $result['master_key']
        ], 201);
    }
}
