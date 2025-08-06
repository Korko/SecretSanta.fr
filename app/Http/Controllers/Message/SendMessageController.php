<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SendMessageController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'recipient_uuid' => 'required|string',
                'content' => 'required|string|max:1000',
                'draw_uuid' => 'required|string'
            ]);

            return response()->json([
                'message' => 'Message sent successfully',
                'message_uuid' => 'msg_' . uniqid()
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send message'
            ], 422);
        }
    }
}
