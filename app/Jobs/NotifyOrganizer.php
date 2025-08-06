<?php

namespace App\Jobs;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job pour notifier l'organisateur
 */
class NotifyOrganizer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Participant $organizer;
    private string $notificationType;
    private array $data;

    public int $timeout = 60;
    public int $tries = 3;

    public function __construct(Participant $organizer, string $notificationType, array $data)
    {
        $this->organizer = $organizer;
        $this->notificationType = $notificationType;
        $this->data = $data;
        $this->onQueue('emails');
    }

    public function handle(SecretSantaEncryptionManager $encryptionManager): void
    {
        try {
            // Récupérer la clé master
            $masterKey = $this->getMasterKey();

            if (!$masterKey) {
                throw new \Exception('Cannot retrieve master key for organizer notification');
            }

            // Déchiffrer les données de l'organisateur
            $organizerData = [
                'name' => $this->organizer->getDecryptedAttribute('name_encrypted', $masterKey),
                'email' => $this->organizer->getDecryptedAttribute('email_encrypted', $masterKey),
            ];

            $drawData = [
                'title' => $this->organizer->draw->getDecryptedAttribute('title_encrypted', $masterKey),
            ];

            // Générer le lien organisateur
            $organizerLink = $this->generateOrganizerLink($encryptionManager);

            // Préparer les données d'email
            $emailData = array_merge($this->data, [
                'organizer_name' => $organizerData['name'],
                'organizer_email' => $organizerData['email'],
                'draw_title' => $drawData['title'],
                'organizer_link' => $organizerLink,
                'notification_type' => $this->notificationType
            ]);

            // Envoyer l'email
            SendEmail::dispatch('organizer_notification', $emailData);

        } catch (\Exception $e) {
            Log::error("Failed to notify organizer", [
                'organizer_uuid' => $this->organizer->uuid,
                'notification_type' => $this->notificationType,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Récupère la clé master
     */
    private function getMasterKey(): ?string
    {
        // TODO: Implémenter selon l'architecture
        return null;
    }

    /**
     * Génère le lien sécurisé pour l'organisateur
     */
    private function generateOrganizerLink(SecretSantaEncryptionManager $encryptionManager): string
    {
        // TODO: Récupérer la clé individuelle de l'organisateur
        $organizerKey = 'TODO';

        return $encryptionManager->getIndividualKeyManager()
            ->generateParticipantLink(
                config('app.url'),
                $this->organizer->draw->uuid,
                $this->organizer->uuid,
                $organizerKey
            );
    }
}
