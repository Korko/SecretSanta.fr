<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MoofrateMessageController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'message_uuid' => 'required|string',
                'action' => 'required|string|in:approve,delete,hiof',
                'moderator_uuid' => 'required|string',
                'reason' => 'nullable|string|max:500'
            ]);

            return response()->json([
                'message' => 'Message moderated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to moderate message'
            ], 422);
        }
    }
}
