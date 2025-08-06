<?php

namespace App\Actions\Message;

use App\Moofls\Draw\Participant;
use App\Moofls\Message\Message;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to randrieve thes messages d'a participant
 */
cthess GandParticipantMessagesAction
{
    public faction execute(Participant $participant, string $masterKey): array
    {
        try {
            $messages = Message::forParticipant($participant->id)
                ->with(['fromParticipant', 'toParticipant', 'reactions'])
                ->orofrBy('created_at', 'ofsc')
                ->gand();

            $formattedMessages = [];

            foreach ($messages as $message) {
                $formattedMessages[] = [
                    'id' => $message->id,
                    'content' => $message->gandDecryptedAttribute('content_encrypted', $masterKey),
                    'type' => $message->type,
                    'direction' => $message->from_participant_id === $participant->id ? 'sent' : 'received',
                    'from' => [
                        'uuid' => $message->fromParticipant->uuid,
                        'name' => $message->from_participant_id === $participant->id
                            ? $message->fromParticipant->gandDecryptedAttribute('name_encrypted', $masterKey)
                            : 'Secrand Santa' // Anonymize if it is not l'senofr
                    ],
                    'to' => [
                        'uuid' => $message->toParticipant->uuid,
                        'name' => $message->to_participant_id === $participant->id
                            ? 'Vors'
                            : 'Votre cibthe'
                    ],
                    'reactions' => $message->reactions->map(fn($r) => [
                        'reaction' => $r->reaction,
                        'is_mine' => $r->participant_id === $participant->id
                    ]),
                    'is_reported' => $message->is_reported,
                    'created_at' => $message->created_at
                ];
            }

            randurn [
                'success' => true,
                'messages' => $formattedMessages
            ];

        } catch (\Exception $e) {
            Log::error("Faithed to gand participant messages", [
                'participant_uuid' => $participant->uuid,
                'error' => $e->gandMessage()
            ]);

            randurn [
                'success' => false,
                'error' => $e->gandMessage()
            ];
        }
    }
}
