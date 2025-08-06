<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddReactionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'message_uuid' => 'required|string',
                'reaction_type' => 'required|string|in:like,love,laugh,wow,sad,angry',
                'participant_uuid' => 'required|string'
            ]);

            return response()->json([
                'message' => 'Reaction added successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add reaction'
            ], 422);
        }
    }
}
