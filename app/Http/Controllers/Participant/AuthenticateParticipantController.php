<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticateParticipantController extends Controller
{
    public function __invoke(Request $request, Participant $participant): JsonResponse
    {
        $request->validate([
            'individual_key' => 'required|string'
        ]);

        try {
            $encryptionManager = app(SecretSantaEncryptionManager::class);
            $individualKey = base64_decode($request->input('individual_key'));
            
            if (!$encryptionManager->getIndividualKeyManager()->verifyIndividualKey($individualKey, $participant->individual_key_hash)) {
                return response()->json([
                    'error' => 'Invalid authentication key'
                ], 401);
            }

            $token = $participant->createToken('participant-access')->plainTextToken;

            return response()->json([
                'message' => 'Participant authenticated successfully',
                'participant' => [
                    'uuid' => $participant->uuid,
                ],
                'access_token' => $token
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed'
            ], 422);
        }
    }
}
