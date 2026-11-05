<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller to get participant details for organizer
 */
class GetParticipantDetailsController extends Controller
{
    public function __invoke(Request $request, Draw $draw, Participant $participant): JsonResponse
    {
        try {
            // Vérifier que le participant appartient bien au tirage
            if ($participant->draw_id !== $draw->id) {
                return response()->json([
                    'error' => 'Participant does not belong to this draw'
                ], 403);
            }

            // Charger les relations nécessaires
            $participant->load([
                'sentMessages' => function ($query) {
                    $query->with('toParticipant:id,uuid,name_encrypted')
                          ->orderBy('created_at', 'desc')
                          ->limit(20);
                },
                'receivedMessages' => function ($query) {
                    $query->with('fromParticipant:id,uuid,name_encrypted')
                          ->orderBy('created_at', 'desc')
                          ->limit(20);
                },
                'exclusions.excludedParticipant:id,uuid,name_encrypted',
                'targetParticipant:id,uuid,name_encrypted',
                'giftGiver:id,uuid,name_encrypted',
            ]);

            $participantDetails = [
                'id' => $participant->id,
                'uuid' => $participant->uuid,
                'name' => $participant->name ?? '[Encrypted]',
                'email' => $participant->email ?? '[Encrypted]',
                'status' => $participant->status,
                'is_organizer' => $participant->is_organizer,
                'created_at' => $participant->created_at,
                'accepted_at' => $participant->accepted_at,
                'email_sent_at' => $participant->email_sent_at,
                'email_opened_at' => $participant->email_opened_at,
                'email_resent_count' => $participant->email_resent_count ?? 0,
                'last_access_at' => $participant->last_access_at,
                'target_participant' => $participant->targetParticipant ? [
                    'uuid' => $participant->targetParticipant->uuid,
                    'name' => $participant->targetParticipant->name ?? '[Encrypted]',
                ] : null,
                'gift_giver' => $participant->giftGiver ? [
                    'uuid' => $participant->giftGiver->uuid,
                    'name' => $participant->giftGiver->name ?? '[Encrypted]',
                ] : null,
            ];

            // Messages envoyés
            $sentMessages = $participant->sentMessages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'type' => $message->type,
                    'to_participant' => [
                        'uuid' => $message->toParticipant->uuid,
                        'name' => $message->toParticipant->name ?? '[Encrypted]',
                    ],
                    'status' => $message->status,
                    'moderation_status' => $message->moderation_status,
                    'created_at' => $message->created_at,
                ];
            });

            // Messages reçus
            $receivedMessages = $participant->receivedMessages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'type' => $message->type,
                    'from_participant' => [
                        'uuid' => $message->fromParticipant->uuid,
                        'name' => $message->fromParticipant->name ?? '[Encrypted]',
                    ],
                    'status' => $message->status,
                    'moderation_status' => $message->moderation_status,
                    'created_at' => $message->created_at,
                ];
            });

            // Exclusions
            $exclusions = $participant->exclusions->map(function ($exclusion) {
                return [
                    'id' => $exclusion->id,
                    'excluded_participant' => [
                        'uuid' => $exclusion->excludedParticipant->uuid,
                        'name' => $exclusion->excludedParticipant->name ?? '[Encrypted]',
                    ],
                    'type' => $exclusion->type,
                    'source' => $exclusion->source,
                    'created_at' => $exclusion->created_at,
                ];
            });

            return response()->json([
                'participant' => $participantDetails,
                'sent_messages' => $sentMessages,
                'received_messages' => $receivedMessages,
                'exclusions' => $exclusions,
                'stats' => [
                    'messages_sent' => $sentMessages->count(),
                    'messages_received' => $receivedMessages->count(),
                    'exclusions_count' => $exclusions->count(),
                    'is_active' => $participant->last_access_at && $participant->last_access_at->gt(now()->subDays(7)),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load participant details'
            ], 500);
        }
    }
}