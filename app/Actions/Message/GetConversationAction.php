<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\Message;
use Illuminate\Support\Facades\Log;

/**
 * Action to retrieve une conversation between two participants
 */
class GetConversationAction
{
    public function execute(
        Participant $participant1,
        Participant $participant2,
        string $masterKey
    ): array {
        try {
            if ($participant1->draw_id !== $participant2->draw_id) {
                throw new \Exception('Participants must belong to the same draw');
            }

            $messages = Message::getConversation($participant1->id, $participant2->id);

            $formattedMessages = [];

            foreach ($messages as $message) {
                $formattedMessages[] = [
                    'id' => $message->id,
                    'content' => $message->getDecryptedAttribute('content_encrypted', $masterKey),
                    'type' => $message->type,
                    'from_participant_id' => $message->from_participant_id,
                    'to_participant_id' => $message->to_participant_id,
                    'reactions' => $message->reactions->map(fn($r) => [
                        'participant_id' => $r->participant_id,
                        'reaction' => $r->reaction
                    ]),
                    'created_at' => $message->created_at
                ];
            }

            return [
                'success' => true,
                'conversation' => $formattedMessages
            ];

        } catch (\Exception $e) {
            Log::error("Failed to get conversation", [
                'participant1_uuid' => $participant1->uuid,
                'participant2_uuid' => $participant2->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
