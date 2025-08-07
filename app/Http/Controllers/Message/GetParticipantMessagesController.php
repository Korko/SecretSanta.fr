<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetParticipantMessagesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'participant_uuid' => 'required|string',
                'draw_uuid' => 'required|string'
            ]);

            return response()->json([
                'messages' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve messages'
            ], 422);
        }
    }
}
