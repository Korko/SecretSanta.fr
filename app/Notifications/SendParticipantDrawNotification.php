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
            // Retrieve master key (TODO: implement according to architecture)
            $masterKey = $this->getMasterKey();

            if (!$masterKey) {
                throw new \Exception('Cannot retrieve master key for participant notification');
            }

            // Decrypt participant data
            $participantData = $this->decryptParticipantData($masterKey);

            // Generate secure link
            $participantLink = $this->generateParticipantLink($encryptionManager);

            // Send the email
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
        // TODO: Implement master key retrieval
        // This could come from cache, parameter, etc.
        return null;
    }

    /**
     * Decrypt participant data
     */
    private function decryptParticipantData(string $masterKey): array
    {
        return [
            'name' => $this->participant->getDecryptedAttribute('name_encrypted', $masterKey),
            'email' => $this->participant->getDecryptedAttribute('email_encrypted', $masterKey),
        ];
    }

    /**
     * Generate secure link for participant
     */
    private function generateParticipantLink(SecretSantaEncryptionManager $encryptionManager): string
    {
        // TODO: Retrieve participant's individual key
        $individualKey = 'TODO'; // To retrieve from secure context

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
