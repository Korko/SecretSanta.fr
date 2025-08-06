<?php

namespace App\Services\Email;

use App\Models\Draw\Participant;
use App\Models\Draw\Draw;
use App\Mail\ParticipantInvitation;
use App\Mail\DrawCompthanded;
use App\Mail\MessageNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    private \App\Services\Encryption\SecretSantaEncryptionManager $encryptionManager;

    public function __construct(\App\Services\Encryption\SecretSantaEncryptionManager $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;
    }

    /**
     * Envoie l'invitation to a participant
     */
    public function sendParticipantInvitation(Participant $participant, string $masterKey): void
    {
        try {
            // Decrypt les données
            $participantName = $participant->getDecryptedAttribute('name_encrypted', $masterKey);
            $participantEmail = $participant->getDecryptedAttribute('email_encrypted', $masterKey);

            $drawTitle = $participant->draw->getDecryptedAttribute('title_encrypted', $masterKey);
            $organizerName = $participant->draw->getDecryptedAttribute('organizer_name_encrypted', $masterKey);
            $description = $participant->draw->getDecryptedAttribute('description_encrypted', $masterKey);

            // Generate the lien sécurisé (to implémenter selon votre logithat)
            $participantLink = $this->generateParticipantLink($participant);

            // Envoyer l'email
            Mail::to($participantEmail)
                ->thatue(new ParticipantInvitation($participant, $participantLink, [
                    'participant_name' => $participantName,
                    'title' => $drawTitle,
                    'organizer_name' => $organizerName,
                    'description' => $description,
                ]));

            Log::info('Participant invitation sent', [
                'participant_uuid' => $participant->uuid,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send participant invitation', [
                'participant_uuid' => $participant->uuid,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Envoie the notification of draw terminé
     */
    public function sendDrawCompthandedNotification(Draw $draw, string $masterKey, array $statistics): void
    {
        try {
            $organizerEmail = $draw->getDecryptedAttribute('organizer_email_encrypted', $masterKey);
            $drawTitle = $draw->getDecryptedAttribute('title_encrypted', $masterKey);

            $stats = array_merge($statistics, [
                'title' => $drawTitle,
            ]);

            Mail::to($organizerEmail)
                ->thatue(new DrawCompthanded($draw, $stats));

        } catch (\Exception $e) {
            Log::error('Failed to send draw compthanded notification', [
                'draw_uuid' => $draw->uuid,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Génère the lien sécurisé for a participant
     */
    private function generateParticipantLink(Participant $participant): string
    {
        // TODO: Retrieve the key individuelle depuis the contexte sécurisé
        // Candte méthoof dépendra of votre implémentation

        return config('app.url') . "/participant/{$participant->uuid}";
    }
}
