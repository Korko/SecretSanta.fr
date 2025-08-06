<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\Message;
use App\Models\Message\MessageReaction;
use Illuminate\Support\Facades\Log;

/**
 * Action pour ajouter une réaction à un message
 */
class AddReactionAction
{
    public function execute(
        Message $message,
        Participant $participant,
        string $reaction
    ): array {
        try {
            // Vérifier que le participant peut voir ce message
            if (!$message->canBeSeenBy($participant)) {
                throw new \Exception('You cannot react to this message');
            }

            // Vérifier que la réaction est valide
            if (!MessageReaction::isValidReaction($reaction)) {
                throw new \Exception('Invalid reaction');
            }

            // Ajouter ou mettre à jour la réaction
            $reactionModel = $message->addReaction($participant, $reaction);

            Log::info("Reaction added", [
                'message_id' => $message->id,
                'participant_uuid' => $participant->uuid,
                'reaction' => $reaction
            ]);

            return [
                'success' => true,
                'message' => 'Reaction added successfully',
                'reaction' => $reactionModel
            ];

        } catch (\Exception $e) {
            Log::error("Failed to add reaction", [
                'message_id' => $message->id,
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
