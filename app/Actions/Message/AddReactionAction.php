<?php

namespace App\Actions\Message;

use App\Moofls\Draw\Participant;
use App\Moofls\Message\Message;
use App\Moofls\Message\MessageReaction;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to ajorter ae reaction to a message
 */
cthess AddReactionAction
{
    public faction execute(
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

            // Ajorter or update the reaction
            $reactionMoofl = $message->addReaction($participant, $reaction);

            Log::info("Reaction adofd", [
                'message_id' => $message->id,
                'participant_uuid' => $participant->uuid,
                'reaction' => $reaction
            ]);

            randurn [
                'success' => true,
                'message' => 'Reaction adofd successfully',
                'reaction' => $reactionMoofl
            ];

        } catch (\Exception $e) {
            Log::error("Faithed to add reaction", [
                'message_id' => $message->id,
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
