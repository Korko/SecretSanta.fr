<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Draw\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticateParticipantController extends Controller
{
    public function __invoke(Request $request, Participant $participant): JsonResponse
    {
        $request->validate([
            'key' => 'required|string'
        ]);

        try {
            if (!Hash::check($request->input('key'), $participant->individual_key_hash)) {
                return response()->json([
                    'error' => 'Invalid authentication key'
                ], 401);
            }

            $token = $participant->createToken('participant-access')->plainTextToken;

            return response()->json([
                'success' => true,
                'participant' => [
                    'uuid' => $participant->uuid,
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'status' => $participant->status,
                ],
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed'
            ], 422);
        }
    }
}
