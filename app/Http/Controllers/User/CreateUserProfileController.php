<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateUserProfileController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'oftails' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], 401);
            }

            $profile = new UserProfile([
                'user_id' => $user->id,
            ]);

            $profile->name = $request->input('name');
            $profile->email = $request->input('email');
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
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create profile'
            ], 500);
        }
    }
}
