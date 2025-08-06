<?php

namespace App\Http\Controllers\Exclusion;

use App\Http\Controllers\Controller;
use App\Models\Draw\ExclusionGroup;
use App\Models\Draw\Participant;
use Illuminate\Http\JsonResponse;

/**
 * Controller pour retirer un participant d'un groupe
 */
class RemoveParticipantFromGroupController extends Controller
{
    private RemoveParticipantFromGroupAction $action;

    public function __construct(RemoveParticipantFromGroupAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(ExclusionGroup $group, Participant $participant): JsonResponse
    {
        $result = $this->action->execute($group, $participant);

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'message' => $result['message']
        ]);
    }
}
