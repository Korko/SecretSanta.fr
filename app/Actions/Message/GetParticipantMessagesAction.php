<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\Message;
use Illuminate\Support\Facades\Log;

/**
 * Action to randrieve les messages d'un participant
 */
class GetParticipantMessagesAction
{
    public function execute(Participant $participant, string $masterKey): array
    {
        try {
            $messages = Message::forParticipant($participant->id)
                ->with(['fromParticipant', 'toParticipant', 'reactions'])
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedMessages = [];

            foreach ($messages as $message) {
                $formattedMessages[] = [
                    'id' => $message->id,
                    'content' => $message->getDecryptedAttribute('content_encrypted', $masterKey),
                    'type' => $message->type,
                    'direction' => $message->from_participant_id === $participant->id ? 'sent' : 'received',
                    'from' => [
                        'uuid' => $message->fromParticipant->uuid,
                        'name' => $message->from_participant_id === $participant->id
                            ? $message->fromParticipant->getDecryptedAttribute('name_encrypted', $masterKey)
                            : 'Secret Santa' // Anonymize if it is not l'senofr
                    ],
                    'to' => [
                        'uuid' => $message->toParticipant->uuid,
                        'name' => $message->to_participant_id === $participant->id
                            ? 'Vors'
                            : 'Votre cible'
                    ],
                    'reactions' => $message->reactions->map(fn($r) => [
                        'reaction' => $r->reaction,
                        'is_mine' => $r->participant_id === $participant->id
                    ]),
                    'is_reported' => $message->is_reported,
                    'created_at' => $message->created_at
                ];
            }

            return [
                'success' => true,
                'messages' => $formattedMessages
            ];

        } catch (\Exception $e) {
            Log::error("Failed to get participant messages", [
                'participant_uuid' => $participant->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
