<?php

namespace App\Http\Controllers\Draw;

use App\Actions\Draw\AddParticipantAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Draw\AddParticipantRequest;
use App\Models\Draw\Draw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller pour ajouter un participant
 */
class AddParticipantController extends Controller
{
    private AddParticipantAction $action;

    public function __construct(AddParticipantAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(AddParticipantRequest $request, Draw $draw): JsonResponse
    {
        // Récupérer la clé master depuis le header d'autorisation
        $masterKey = $this->extractMasterKey($request);

        if (!$masterKey) {
            return response()->json(['error' => 'Master key required'], 401);
        }

        $result = $this->action->execute(
            $draw,
            $request->validated(),
            $masterKey
        );

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'participant' => [
                'uuid' => $result['participant']->uuid,
                'status' => $result['participant']->status,
            ],
            'participant_link' => $result['participant_link']
        ], 201);
    }

    private function extractMasterKey(Request $request): ?string
    {
        $authHeader = $request->header('X-Master-Key');
        return $authHeader ? base64_decode($authHeader) : null;
    }
}
