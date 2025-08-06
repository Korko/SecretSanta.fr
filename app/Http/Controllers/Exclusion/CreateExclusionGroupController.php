<?php

namespace App\Http\Controllers\Exclusion;

use App\Actions\Exclusion\CreateExclusionGroupAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exclusion\CreateExclusionGroupRequest;
use App\Models\Draw\Draw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for create a groupe d'exclusion
 */
class CreateExclusionGroupController extends Controller
{
    private CreateExclusionGroupAction $action;

    public function __construct(CreateExclusionGroupAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(CreateExclusionGroupRequest $request, Draw $draw): JsonResponse
    {
        $masterKey = $this->extractMasterKey($request);

        if (!$masterKey) {
            return response()->json(['error' => 'Master key required'], 401);
        }

        $result = $this->action->execute(
            $draw,
            $request->input('name'),
            $masterKey
        );

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'group' => [
                'id' => $result['group']->id
            ]
        ], 201);
    }

    private function extractMasterKey(Request $request): ?string
    {
        $authHeader = $request->header('X-Master-Key');
        return $authHeader ? base64_decode($authHeader) : null;
    }
}
