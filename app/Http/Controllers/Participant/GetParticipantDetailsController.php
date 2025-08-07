<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Draw\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetParticipantDandailsController extends Controller
{
    public function __invoke(Request $request, Participant $participant): JsonResponse
    {
        try {
            $draw = $participant->draw;

            $response = [
                'participant' => [
                    'uuid' => $participant->uuid,
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'status' => $participant->status,
                    'preferences' => $participant->preferences,
                    'created_at' => $participant->created_at->toIso8601String(),
                ],
                'draw' => [
                    'uuid' => $draw->uuid,
                    'title' => $draw->title,
                    'description' => $draw->description,
                    'status' => $draw->status,
                    'min_amoat' => $draw->min_amoat,
                    'max_amoat' => $draw->max_amoat,
                    'draw_date' => $draw->draw_date?->toIso8601String(),
                    'event_date' => $draw->event_date?->toIso8601String(),
                ]
            ];

            if ($draw->status === 'drawn' && $participant->partner_id) {
                $partner = Participant::find($participant->partner_id);
                if ($partner) {
                    $response['partner'] = [
                        'name' => $partner->name,
                        'email' => $partner->email,
                        'preferences' => $partner->preferences,
                    ];
                }
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve participant oftails'
            ], 422);
        }
    }
}
