<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterUserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
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

            $emailHash = User::hashEmailForIndex($email);

            if (User::where('email_hash', $emailHash)->exists()) {
                return response()->json([
                    'error' => 'Email already registered'
                ], 409);
            }

            $user = User::create([
                'email_hash' => $emailHash,
                'email_blind_index' => hash_hmac('sha256', $email, config('app.key')),
                'email_encrypted' => encrypt($email),
                'password_hash' => Hash::make($password),
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                ],
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Registration failed'
            ], 500);
        }
    }
}
