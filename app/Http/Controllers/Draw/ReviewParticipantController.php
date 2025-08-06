<?php

namespace App\Http\Controllers\Draw;

use App\Actions\Draw\ReviewParticipantAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Draw\ReviewParticipantRequest;
use App\Models\Draw\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller pour accepter/refuser un participant
 */
class ReviewParticipantController extends Controller
{
    private ReviewParticipantAction $action;

    public function __construct(ReviewParticipantAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(ReviewParticipantRequest $request, Participant $participant): JsonResponse
    {
        $masterKey = $this->extractMasterKey($request);

        if (!$masterKey) {
            return response()->json(['error' => 'Master key required'], 401);
        }

        $result = $this->action->execute(
            $participant,
            $request->input('action'),
            $masterKey
        );

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'participant' => [
                'uuid' => $result['participant']->uuid,
                'status' => $result['participant']->status,
            ]
        ]);
    }

    private function extractMasterKey(Request $request): ?string
    {
        $authHeader = $request->header('X-Master-Key');
        return $authHeader ? base64_decode($authHeader) : null;
    }
}
