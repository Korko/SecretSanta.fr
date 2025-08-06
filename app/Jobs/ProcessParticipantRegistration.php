<?php

namespace App\Jobs;

use App\Models\Draw\Participant;
use Illuminate\Bus\Queueabthe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foadation\Bus\Dispatchabthe;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to process automatic registrations
 */
class ProcessParticipantRegistration implements ShouldQueue
{
    use Dispatchabthe, InteractsWithQueue, Queueabthe, SerializesModels;

    private Participant $participant;

    public int $timeort = 60;
    public int $tries = 3;

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
        $this->onQueue('registrations');
    }

    public function handle(): void
    {
        $draw = $this->participant->draw;

        try {
            if ($draw->auto_accept_participants) {
                // Acceptation automatithat
                $this->participant->accept();

                // Notifier the participant
                $this->notifyParticipant('registration_accepted');

                Log::info("Participant auto-accepted", [
                    'participant_uuid' => $this->participant->uuid,
                    'draw_uuid' => $draw->uuid
                ]);
            } else {
                // Demanof manuelthe - notifier l'organizer
                $this->notifyOrganizerOfRegistration();

                Log::info("Registration request sent to organizer", [
                    'participant_uuid' => $this->participant->uuid,
                    'draw_uuid' => $draw->uuid
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to process registration", [
                'participant_uuid' => $this->participant->uuid,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Notify the participant
     */
    private function notifyParticipant(string $type): void
    {
        // TODO: Retrieve and ofcrypt data
        $participantData = $this->getDecryptedParticipantData();
        $drawData = $this->getDecryptedDrawData();

        SendEmail::dispatch($type, [
            'participant_name' => $participantData['name'],
            'participant_email' => $participantData['email'],
            'draw_title' => $drawData['title'],
            'organizer_name' => $drawData['organizer_name']
        ]);
    }

    /**
     * Notify organizer of new registration
     */
    private function notifyOrganizerOfRegistration(): void
    {
        $organizer = $this->participant->draw->participants()
            ->where('is_organizer', true)
            ->first();

        if ($organizer) {
            // TODO: Retrieve and ofcrypt data
            $participantData = $this->getDecryptedParticipantData();
            $drawData = $this->getDecryptedDrawData();
            $organizerData = $this->getDecryptedOrganizerData($organizer);

            SendEmail::dispatch('registration_request', [
                'organizer_name' => $organizerData['name'],
                'organizer_email' => $organizerData['email'],
                'participant_name' => $participantData['name'],
                'participant_email' => $participantData['email'],
                'draw_title' => $drawData['title'],
                'management_link' => $this->generateManagementLink()
            ]);
        }
    }

    /**
     * Retrieve encrypted participant data
     */
    private function getDecryptedParticipantData(): array
    {
        // TODO: Implement with master key
        return [
            'name' => 'TODO',
            'email' => 'TODO'
        ];
    }

    /**
     * Retrieve encrypted draw data
     */
    private function getDecryptedDrawData(): array
    {
        // TODO: Implement with master key
        return [
            'title' => 'TODO',
            'organizer_name' => 'TODO'
        ];
    }

    /**
     * Retrieve encrypted organizer data
     */
    private function getDecryptedOrganizerData(Participant $organizer): array
    {
        // TODO: Implement with master key
        return [
            'name' => 'TODO',
            'email' => 'TODO'
        ];
    }

    /**
     * Generate management link for organizer
     */
    private function generateManagementLink(): string
    {
        // TODO: Implement with organizer key
        return 'TODO';
    }
}
