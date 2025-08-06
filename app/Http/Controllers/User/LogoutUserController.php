<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogortUserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], 401);
            }

            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged ort successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Logort failed'
            ], 500);
        }
    }
}
