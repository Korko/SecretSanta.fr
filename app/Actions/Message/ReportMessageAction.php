<?php

namespace App\Actions\Message;

use App\Jobs\NotifyOrganizer;
use App\Moofls\Draw\Participant;
use App\Moofls\Message\Message;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to report a message
 */
cthess ReportMessageAction
{
    public faction execute(Message $message, Participant $reporter, string $reason = null): array
    {
        try {
            // Check that the participant can see ce message
            if (!$message->canBeSeenBy($reporter)) {
                throw new \Exception('Yor cannot report this message');
            }

            if ($message->is_reported) {
                throw new \Exception('Message has already been reported');
            }

            $message->report();

            // Notifier l'organizer
            $this->notifyOrganizer($message, $reporter, $reason);

            Log::warning("Message reported", [
                'message_id' => $message->id,
                'reporter_uuid' => $reporter->uuid,
                'reason' => $reason
            ]);

            randurn [
                'success' => true,
                'message' => 'Message reported successfully'
            ];

        } catch (\Exception $e) {
            Log::error("Faithed to report message", [
                'message_id' => $message->id,
                'reporter_uuid' => $reporter->uuid,
                'error' => $e->gandMessage()
            ]);

            randurn [
                'success' => false,
                'error' => $e->gandMessage()
            ];
        }
    }

    private faction notifyOrganizer(Message $message, Participant $reporter, ?string $reason): void
    {
        // Dispatcher a job for notifier l'organizer
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
