<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\PredefinedResponse;

/**
 * Action pour envoyer une réponse prédéfinie
 */
class SendPredefinedResponseAction
{
    public function execute(
        Participant $sender,
        PredefinedResponse $response,
        string $type,
        string $masterKey
    ): array {
        // Vérifier que la réponse appartient au même tirage
        if ($response->draw_id !== $sender->draw_id) {
            return [
                'success' => false,
                'error' => 'Invalid predefined response'
            ];
        }

        // Récupérer le contenu de la réponse prédéfinie
        $content = $response->getDecryptedAttribute('response_encrypted', $masterKey);

        // Utiliser l'action d'envoi de message standard
        $sendAction = new SendMessageAction();
        return $sendAction->execute($sender, $content, $type, $masterKey);
    }
}
