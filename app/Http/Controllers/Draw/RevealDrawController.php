<?php

namespace App\Http\Controllers\Draw;

use App\Actions\Draw\RevealDrawAction;
use App\Http\Controllers\Controller;
use App\Models\Draw\Draw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller pour révéler les résultats
 */
class RevealDrawController extends Controller
{
    private RevealDrawAction $action;

    public function __construct(RevealDrawAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(Request $request, Draw $draw): JsonResponse
    {
        $masterKey = $this->extractMasterKey($request);

        if (!$masterKey) {
            return response()->json(['error' => 'Master key required'], 401);
        }

        $result = $this->action->execute($draw, $masterKey);

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'assignments' => $result['assignments'],
            'draw' => [
                'uuid' => $result['draw']->uuid,
                'status' => $result['draw']->status,
            ]
        ]);
    }

    private function extractMasterKey(Request $request): ?string
    {
        $authHeader = $request->header('X-Master-Key');
        return $authHeader ? base64_decode($authHeader) : null;
    }
}
