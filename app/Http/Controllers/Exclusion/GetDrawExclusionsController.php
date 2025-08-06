<?php

namespace App\Http\Controllers\Exclusion;

use App\Actions\Exclusion\GetDrawExclusionsAction;
use App\Http\Controllers\Controller;
use App\Models\Draw\Draw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller pour récupérer toutes les exclusions d'un tirage
 */
class GetDrawExclusionsController extends Controller
{
    private GetDrawExclusionsAction $action;

    public function __construct(GetDrawExclusionsAction $action)
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
            'individual_exclusions' => $result['individual_exclusions'],
            'exclusion_groups' => $result['exclusion_groups']
        ]);
    }

    private function extractMasterKey(Request $request): ?string
    {
        $authHeader = $request->header('X-Master-Key');
        return $authHeader ? base64_decode($authHeader) : null;
    }
}
