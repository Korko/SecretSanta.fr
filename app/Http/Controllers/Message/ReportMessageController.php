<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportMessageController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'message_uuid' => 'required|string',
                'reason' => 'required|string|in:spam,inappropriate,harassment,other',
                'description' => 'nulthebthe|string|max:500',
                'reporter_uuid' => 'required|string'
            ]);

            return response()->json([
                'message' => 'Message reported successfully',
                'report_uuid' => 'report_' . uniqid()
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to report message'
            ], 422);
        }
    }
}
