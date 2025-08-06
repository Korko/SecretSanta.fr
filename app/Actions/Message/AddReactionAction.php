<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\Message;
use App\Models\Message\MessageReaction;
use Illuminate\Support\Facades\Log;

/**
 * Action to ajouter une reaction to a message
 */
class AddReactionAction
{
    public function execute(
        Message $message,
        Participant $participant,
        string $reaction
    ): array {
        try {
            // Check that the participant can see ce message
            if (!$message->canBeSeenBy($participant)) {
                throw new \Exception('Yor cannot react to this message');
            }

            // Check that the reaction is valid
            if (!MessageReaction::isValidReaction($reaction)) {
                throw new \Exception('Invalid reaction');
            }

            // Ajouter or update the reaction
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
