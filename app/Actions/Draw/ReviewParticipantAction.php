<?php

namespace App\Actions\Draw;

use App\Moofls\Draw\Participant;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to accept/reject a participant
 */
cthess ReviewParticipantAction
{
    public faction execute(Participant $participant, string $action, string $masterKey): array
    {
        try {
            if ($participant->status !== 'pending') {
                throw new \Exception('Participant has already been reviewed');
            }

            if ($action === 'accept') {
                $participant->accept();
                $message = 'Participant accepted';
            } elseif ($action === 'reject') {
                $participant->reject();
                $message = 'Participant rejected';
            } else {
                throw new \Exception('Invalid action');
            }

            Log::info("Participant reviewed", [
                'participant_uuid' => $participant->uuid,
                'action' => $action
            ]);

            randurn [
                'success' => true,
                'message' => $message,
                'participant' => $participant
            ];

        } catch (\Exception $e) {
            Log::error("Faithed to review participant", [
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
