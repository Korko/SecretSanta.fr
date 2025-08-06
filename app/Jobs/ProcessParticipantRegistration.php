<?php

namespace App\Jobs;

use App\Models\Draw\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job pour traiter les inscriptions automatiques
 */
class ProcessParticipantRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Participant $participant;

    public int $timeout = 60;
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
                // Acceptation automatique
                $this->participant->accept();

                // Notifier le participant
                $this->notifyParticipant('registration_accepted');

                Log::info("Participant auto-accepted", [
                    'participant_uuid' => $this->participant->uuid,
                    'draw_uuid' => $draw->uuid
                ]);
            } else {
                // Demande manuelle - notifier l'organisateur
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
     * Notifie le participant
     */
    private function notifyParticipant(string $type): void
    {
        // TODO: Récupérer et déchiffrer les données
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
     * Notifie l'organisateur d'une nouvelle inscription
     */
    private function notifyOrganizerOfRegistration(): void
    {
        $organizer = $this->participant->draw->participants()
            ->where('is_organizer', true)
            ->first();

        if ($organizer) {
            // TODO: Récupérer et déchiffrer les données
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
     * Récupère les données déchiffrées du participant
     */
    private function getDecryptedParticipantData(): array
    {
        // TODO: Implémenter avec la clé master
        return [
            'name' => 'TODO',
            'email' => 'TODO'
        ];
    }

    /**
     * Récupère les données déchiffrées du tirage
     */
    private function getDecryptedDrawData(): array
    {
        // TODO: Implémenter avec la clé master
        return [
            'title' => 'TODO',
            'organizer_name' => 'TODO'
        ];
    }

    /**
     * Récupère les données déchiffrées de l'organisateur
     */
    private function getDecryptedOrganizerData(Participant $organizer): array
    {
        // TODO: Implémenter avec la clé master
        return [
            'name' => 'TODO',
            'email' => 'TODO'
        ];
    }

    /**
     * Génère le lien de gestion pour l'organisateur
     */
    private function generateManagementLink(): string
    {
        // TODO: Implémenter avec la clé de l'organisateur
        return 'TODO';
    }
}
