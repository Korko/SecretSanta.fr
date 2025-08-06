<?php

namespace App\Actions\Message;

use App\Jobs\NotifyOrganizer;
use App\Models\Draw\Participant;
use App\Models\Message\Message;
use Illuminate\Support\Facades\Log;

/**
 * Action pour signaler un message
 */
class ReportMessageAction
{
    public function execute(Message $message, Participant $reporter, string $reason = null): array
    {
        try {
            // Vérifier que le participant peut voir ce message
            if (!$message->canBeSeenBy($reporter)) {
                throw new \Exception('You cannot report this message');
            }

            if ($message->is_reported) {
                throw new \Exception('Message has already been reported');
            }

            $message->report();

            // Notifier l'organisateur
            $this->notifyOrganizer($message, $reporter, $reason);

            Log::warning("Message reported", [
                'message_id' => $message->id,
                'reporter_uuid' => $reporter->uuid,
                'reason' => $reason
            ]);

            return [
                'success' => true,
                'message' => 'Message reported successfully'
            ];

        } catch (\Exception $e) {
            Log::error("Failed to report message", [
                'message_id' => $message->id,
                'reporter_uuid' => $reporter->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function notifyOrganizer(Message $message, Participant $reporter, ?string $reason): void
    {
        // Dispatcher un job pour notifier l'organisateur
        NotifyOrganizer::dispatch(
            $message->draw->participants()->where('is_organizer', true)->first(),
            'message_reported',
            [
                'message_id' => $message->id,
                'reporter_uuid' => $reporter->uuid,
                'reason' => $reason
            ]
        );
    }
}
