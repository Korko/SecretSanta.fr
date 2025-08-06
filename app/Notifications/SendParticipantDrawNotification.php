<?php

namespace App\Notifications;

use App\Jobs\SendEmail;
use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job pour envoyer les notifications aux participants
 */
class SendParticipantDrawNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Participant $participant;

    public int $timeout = 60;
    public int $tries = 5;

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
        $this->onQueue('emails');
    }

    public function handle(SecretSantaEncryptionManager $encryptionManager): void
    {
        try {
            // Récupérer la clé master (TODO: implémenter selon l'architecture)
            $masterKey = $this->getMasterKey();

            if (!$masterKey) {
                throw new \Exception('Cannot retrieve master key for participant notification');
            }

            // Déchiffrer les données du participant
            $participantData = $this->decryptParticipantData($masterKey);

            // Générer le lien sécurisé
            $participantLink = $this->generateParticipantLink($encryptionManager);

            // Envoyer l'email
            $this->sendEmail($participantData, $participantLink);

            Log::info("Participant notification sent", [
                'participant_uuid' => $this->participant->uuid,
                'draw_uuid' => $this->participant->draw->uuid
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send participant notification", [
                'participant_uuid' => $this->participant->uuid,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Récupère la clé master (à implémenter selon l'architecture)
     */
    private function getMasterKey(): ?string
    {
        // TODO: Implémenter la récupération de la clé master
        // Cela pourrait venir du cache, d'un paramètre, etc.
        return null;
    }

    /**
     * Déchiffre les données du participant
     */
    private function decryptParticipantData(string $masterKey): array
    {
        return [
            'name' => $this->participant->getDecryptedAttribute('name_encrypted', $masterKey),
            'email' => $this->participant->getDecryptedAttribute('email_encrypted', $masterKey),
        ];
    }

    /**
     * Génère le lien sécurisé pour le participant
     */
    private function generateParticipantLink(SecretSantaEncryptionManager $encryptionManager): string
    {
        // TODO: Récupérer la clé individuelle du participant
        $individualKey = 'TODO'; // À récupérer depuis le contexte sécurisé

        return $encryptionManager->getIndividualKeyManager()
            ->generateParticipantLink(
                config('app.url'),
                $this->participant->draw->uuid,
                $this->participant->uuid,
                $individualKey
            );
    }

    /**
     * Envoie l'email au participant
     */
    private function sendEmail(array $participantData, string $link): void
    {
        SendEmail::dispatch('participant_draw_ready', [
            'participant_name' => $participantData['name'],
            'participant_email' => $participantData['email'],
            'draw_title' => $this->participant->draw->title ?? 'Secret Santa',
            'participant_link' => $link,
            'draw_uuid' => $this->participant->draw->uuid
        ]);
    }
}
