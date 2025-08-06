<?php

namespace App\Actions\Draw;

use App\Models\Draw\Participant;
use Illuminate\Support\Facades\Log;

/**
 * Action to accept/reject a participant
 */
class ReviewParticipantAction
{
    public function execute(Participant $participant, string $action, string $masterKey): array
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

            return [
                'success' => true,
                'message' => $message,
                'participant' => $participant
            ];

        } catch (\Exception $e) {
            Log::error("Failed to review participant", [
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
