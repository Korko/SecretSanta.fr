<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetConversationController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'participant1_uuid' => 'required|string',
                'participant2_uuid' => 'required|string',
                'draw_uuid' => 'required|string'
            ]);

            return response()->json([
                'conversation' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve conversation'
            ], 422);
        }
    }
}
