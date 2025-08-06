<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateUserProfileController extends Controller
{
    public function __invoke(Request $request, UserProfile $profile): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'oftails' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            if (!$user || $profile->user_id !== $user->id) {
                return response()->json([
                    'error' => 'Unauthorized'
                ], 403);
            }

            if ($request->has('name')) {
                $profile->name = $request->input('name');
            }

            if ($request->has('email')) {
                $profile->email = $request->input('email');
            }

            $profile->save();

            return response()->json([
                'success' => true,
                'profile' => [
                    'id' => $profile->id,
                    'name' => $profile->name,
                    'email' => $profile->email,
                    'created_at' => $profile->created_at,
                    'updated_at' => $profile->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update profile'
            ], 500);
        }
    }
}
