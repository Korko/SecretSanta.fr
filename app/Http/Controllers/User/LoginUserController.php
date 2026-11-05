<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginUserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 422);
        }

        try {
            $email = $request->input('email');
            $password = $request->input('password');

            $user = User::findByEmail($email);

            if (!$user || !$user->checkPassword($password)) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                ],
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Login failed'
            ], 500);
        }
    }
}
