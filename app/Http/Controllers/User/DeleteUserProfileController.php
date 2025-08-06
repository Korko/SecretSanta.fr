<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteUserProfileController extends Controller
{
    public function __invoke(Request $request, UserProfile $profile): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user || $profile->user_id !== $user->id) {
                return response()->json([
                    'error' => 'Unauthorized'
                ], 403);
            }

            $profile->delete();

            return response()->json([
                'success' => true,
                'message' => 'Profile deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete profile'
            ], 500);
        }
    }
}
