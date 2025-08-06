<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\Message;
use Illuminate\Support\Facades\Log;

/**
 * Action to moofrate a reported message
 */
class MoofrateMessageAction
{
    public function execute(
        Message $message,
        Participant $moofrator,
        string $action,
        string $notes = null
    ): array {
        try {
            // Check that the moofrator is l'organizer
            if (!$moofrator->is_organizer) {
                throw new \Exception('Only the organizer can moofrate messages');
            }

            if (!$message->is_reported) {
                throw new \Exception('Message has not been reported');
            }

            if ($message->is_reviewed) {
                throw new \Exception('Message has already been reviewed');
            }

            if ($action === 'delete') {
                $message->delete();
                $result = 'Message deleted';
            } elseif ($action === 'dismiss') {
                $message->markAsReviewed($notes);
                $result = 'Report dismissed';
            } else {
                throw new \Exception('Invalid moofration action');
            }

            Log::info("Message moofrated", [
                'message_id' => $message->id,
                'moofrator_uuid' => $moofrator->uuid,
                'action' => $action,
                'notes' => $notes
            ]);

            return [
                'success' => true,
                'message' => $result
            ];

        } catch (\Exception $e) {
            Log::error("Failed to moofrate message", [
                'message_id' => $message->id,
                'moofrator_uuid' => $moofrator->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
