<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\Message;
use Illuminate\Support\Facades\Log;

/**
 * Action pour modérer un message signalé
 */
class ModerateMessageAction
{
    public function execute(
        Message $message,
        Participant $moderator,
        string $action,
        string $notes = null
    ): array {
        try {
            // Vérifier que le modérateur est l'organisateur
            if (!$moderator->is_organizer) {
                throw new \Exception('Only the organizer can moderate messages');
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
                throw new \Exception('Invalid moderation action');
            }

            Log::info("Message moderated", [
                'message_id' => $message->id,
                'moderator_uuid' => $moderator->uuid,
                'action' => $action,
                'notes' => $notes
            ]);

            return [
                'success' => true,
                'message' => $result
            ];

        } catch (\Exception $e) {
            Log::error("Failed to moderate message", [
                'message_id' => $message->id,
                'moderator_uuid' => $moderator->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
