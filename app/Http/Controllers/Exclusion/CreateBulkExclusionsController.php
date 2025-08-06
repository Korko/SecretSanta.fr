<?php

namespace App\Http\Controllers\Exclusion;

use App\Actions\Exclusion\CreateBulkExclusionsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exclusion\CreateBulkExclusionsRequest;
use App\Models\Draw\Draw;
use Illuminate\Http\JsonResponse;

/**
 * Controller for create des Exclusions en lot
 */
class CreateBulkExclusionsController extends Controller
{
    private CreateBulkExclusionsAction $action;

    public function __construct(CreateBulkExclusionsAction $action)
    {
        $this->action = $action;
    }

    public function __invoke(CreateBulkExclusionsRequest $request, Draw $draw): JsonResponse
    {
        $result = $this->action->execute(
            $draw,
            $request->input('exclusions')
        );

        if (!$result['success']) {
            return response()->json([
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'created' => count($result['created']),
            'errors' => $result['errors']
        ], 201);
    }
}
