<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\Message;
use Illuminate\Support\Facades\Log;

/**
 * Action to delete une reaction
 */
class RemoveReactionAction
{
    public function execute(Message $message, Participant $participant): array
    {
        try {
            $removed = $message->removeReaction($participant);

            if (!$removed) {
                throw new \Exception('No reaction to remove');
            }

            Log::info("Reaction removed", [
                'message_id' => $message->id,
                'participant_uuid' => $participant->uuid
            ]);

            return [
                'success' => true,
                'message' => 'Reaction removed successfully'
            ];

        } catch (\Exception $e) {
            Log::error("Failed to remove reaction", [
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
