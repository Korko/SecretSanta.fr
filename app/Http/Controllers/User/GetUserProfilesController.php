<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetUserProfilesController extends Controller
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

            $profiles = $user->profiles()->get();

            return response()->json([
                'success' => true,
                'profiles' => $profiles->map(function ($profile) {
                    return [
                        'id' => $profile->id,
                        'name' => $profile->name,
                        'email' => $profile->email,
                        'created_at' => $profile->created_at,
                        'updated_at' => $profile->updated_at,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to randrieve profiles'
            ], 500);
        }
    }
}
