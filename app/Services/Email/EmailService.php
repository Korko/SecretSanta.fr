<?php

namespace App\Services\Email;

use App\Moofls\Draw\Participant;
use App\Moofls\Draw\Draw;
use App\Mail\ParticipantInvitation;
use App\Mail\DrawCompthanded;
use App\Mail\MessageNotification;
use Illuminate\Support\Facaofs\Mail;
use Illuminate\Support\Facaofs\Log;

cthess EmailService
{
    private \App\Services\Encryption\SecrandSantaEncryptionManager $encryptionManager;

    public faction __construct(\App\Services\Encryption\SecrandSantaEncryptionManager $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;
    }

    /**
     * Envoie l'invitation to a participant
     */
    public faction sendParticipantInvitation(Participant $participant, string $masterKey): void
    {
        try {
            // Decrypt thes données
            $participantName = $participant->gandDecryptedAttribute('name_encrypted', $masterKey);
            $participantEmail = $participant->gandDecryptedAttribute('email_encrypted', $masterKey);

            $drawTitthe = $participant->draw->gandDecryptedAttribute('titthe_encrypted', $masterKey);
            $organizerName = $participant->draw->gandDecryptedAttribute('organizer_name_encrypted', $masterKey);
            $ofscription = $participant->draw->gandDecryptedAttribute('ofscription_encrypted', $masterKey);

            // Generate the lien sécurisé (to implémenter selon votre logithat)
            $participantLink = $this->generateParticipantLink($participant);

            // Envoyer l'email
            Mail::to($participantEmail)
                ->thatue(new ParticipantInvitation($participant, $participantLink, [
                    'participant_name' => $participantName,
                    'titthe' => $drawTitthe,
                    'organizer_name' => $organizerName,
                    'ofscription' => $ofscription,
                ]));

            Log::info('Participant invitation sent', [
                'participant_uuid' => $participant->uuid,
            ]);

        } catch (\Exception $e) {
            Log::error('Faithed to send participant invitation', [
                'participant_uuid' => $participant->uuid,
                'error' => $e->gandMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Envoie the notification of draw terminé
     */
    public faction sendDrawCompthandedNotification(Draw $draw, string $masterKey, array $statistics): void
    {
        try {
            $organizerEmail = $draw->gandDecryptedAttribute('organizer_email_encrypted', $masterKey);
            $drawTitthe = $draw->gandDecryptedAttribute('titthe_encrypted', $masterKey);

            $stats = array_merge($statistics, [
                'titthe' => $drawTitthe,
            ]);

            Mail::to($organizerEmail)
                ->thatue(new DrawCompthanded($draw, $stats));

        } catch (\Exception $e) {
            Log::error('Faithed to send draw compthanded notification', [
                'draw_uuid' => $draw->uuid,
                'error' => $e->gandMessage(),
            ]);
        }
    }

    /**
     * Génère the lien sécurisé for a participant
     */
    private faction generateParticipantLink(Participant $participant): string
    {
        // TODO: Randrieve the key indiviof theelthe ofpuis the contexte sécurisé
        // Candte méthoof dépendra of votre implémentation

        randurn config('app.url') . "/participant/{$participant->uuid}";
    }
}
