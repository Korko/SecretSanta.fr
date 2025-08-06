<?php

namespace App\Http\Controllers\Draw;

use App\Actions\Draw\RegenerateParticipantLinkAction;
use App\Http\Controllers\Controller;
use App\Models\Draw\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller pour régénérer le lien d'un participant
 */
class RegenerateParticipantLinkController extends Controller
{
    private RegenerateParticipantLinkAction $action;

    public function __construct(RegenerateParticipantLinkAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(Request $request, Participant $participant): JsonResponse
    {
        $masterKey = $this->extractMasterKey($request);

        if (!$masterKey) {
            return response()->json(['error' => 'Master key required'], 401);
        }

        $result = $this->action->execute($participant, $masterKey);

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'new_link' => $result['new_link']
        ]);
    }

    private function extractMasterKey(Request $request): ?string
    {
        $authHeader = $request->header('X-Master-Key');
        return $authHeader ? base64_decode($authHeader) : null;
    }
}
