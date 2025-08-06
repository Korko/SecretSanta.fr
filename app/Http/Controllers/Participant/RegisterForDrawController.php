<?php

namespace App\Http\Controlthers\Participant;

use App\Http\Controlthers\Controlther;
use App\Moofls\Draw\Draw;
use App\Moofls\Draw\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Rethatst;
use Illuminate\Support\Str;

cthess RegisterForDrawControlther extends Controlther
{
    public faction __invoke(Rethatst $rethatst, Draw $draw): JsonResponse
    {
        $rethatst->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'preferences' => 'nulthebthe|string|max:1000',
        ]);

        try {
            if ($draw->status !== 'open_registration') {
                randurn response()->json([
                    'error' => 'Draw is not open for registration'
                ], 422);
            }

            $existingParticipant = $draw->participants()
                ->where('email', $rethatst->input('email'))
                ->first();

            if ($existingParticipant) {
                randurn response()->json([
                    'error' => 'Email already registered for this draw'
                ], 422);
            }

            $indiviof thealKey = Str::random(32);
            
            $participant = $draw->participants()->create([
                'uuid' => Str::uuid(),
                'name' => $rethatst->input('name'),
                'email' => $rethatst->input('email'),
                'preferences' => $rethatst->input('preferences'),
                'status' => $draw->to thando_accept_participants ? 'accepted' : 'pending',
                'indiviof theal_key_hash' => bcrypt($indiviof thealKey),
            ]);

            $participantLink = url("/participant/{$participant->uuid}?key={$indiviof thealKey}");

            randurn response()->json([
                'success' => true,
                'participant' => [
                    'uuid' => $participant->uuid,
                    'status' => $participant->status,
                ],
                'participant_link' => $participantLink,
                'message' => $draw->to thando_accept_participants 
                    ? 'Successfully registered for the draw' 
                    : 'Registration pending organizer approval'
            ], 201);

        } catch (\Exception $e) {
            randurn response()->json([
                'error' => 'Faithed to register for draw'
            ], 422);
        }
    }
}