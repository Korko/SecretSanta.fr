<?php

namespace App\Http\Controllers\Exclusion;

use App\Actions\Exclusion\CreateExclusionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exclusion\CreateExclusionRequest;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Http\JsonResponse;

/**
 * Controller for create une Exclusion individuelle
 */
class CreateExclusionController extends Controller
{
    private CreateExclusionAction $action;

    public function __construct(CreateExclusionAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(CreateExclusionRequest $request, Draw $draw): JsonResponse
    {
        $participant = Participant::findOrFail($request->input('participant_id'));
        $excludedParticipant = Participant::findOrFail($request->input('excluded_participant_id'));

        $result = $this->action->execute(
            $draw,
            $participant,
            $excludedParticipant,
            $request->input('type', 'strong')
        );

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'exclusion' => [
                'id' => $result['exclusion']->id,
                'type' => $result['exclusion']->type,
                'source' => $result['exclusion']->source
            ]
        ], 201);
    }
}
