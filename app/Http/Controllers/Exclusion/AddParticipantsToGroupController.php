<?php

namespace App\Http\Controllers\Exclusion;

use App\Actions\Exclusion\AddParticipantsToGroupAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exclusion\AddParticipantsToGroupRequest;
use App\Models\Draw\ExclusionGroup;
use Illuminate\Http\JsonResponse;

/**
 * Controller pour ajouter des participants à un groupe
 */
class AddParticipantsToGroupController extends Controller
{
    private AddParticipantsToGroupAction $action;

    public function __construct(AddParticipantsToGroupAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(AddParticipantsToGroupRequest $request, ExclusionGroup $group): JsonResponse
    {
        $result = $this->action->execute(
            $group,
            $request->input('participant_ids')
        );

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'added' => count($result['added']),
            'errors' => $result['errors']
        ]);
    }
}
