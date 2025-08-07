<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterForDrawController extends Controller
{
    public function __invoke(Request $request, Draw $draw): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'preferences' => 'nullable|string|max:1000',
        ]);

        try {
            if ($draw->status !== 'open_registration') {
                return response()->json([
                    'error' => 'Draw is not open for registration'
                ], 422);
            }

            $existingParticipant = $draw->participants()
                ->where('email', $request->input('email'))
                ->first();

            if ($existingParticipant) {
                return response()->json([
                    'error' => 'Email already registered for this draw'
                ], 422);
            }

            $individualKey = Str::random(32);

            $participant = $draw->participants()->create([
                'uuid' => Str::uuid(),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'preferences' => $request->input('preferences'),
                'status' => $draw->auto_accept_participants ? 'accepted' : 'pending',
                'individual_key_hash' => bcrypt($individualKey),
            ]);

            $participantLink = url("/participant/{$participant->uuid}?key={$individualKey}");

            return response()->json([
                'success' => true,
                'participant' => [
                    'uuid' => $participant->uuid,
                    'status' => $participant->status,
                ],
                'participant_link' => $participantLink,
                'message' => $draw->auto_accept_participants
                    ? 'Successfully registered for the draw'
                    : 'Registration pending organizer approval'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to register for draw'
            ], 422);
        }
    }
}
