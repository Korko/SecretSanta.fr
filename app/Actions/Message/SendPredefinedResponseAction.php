<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\PredefinedResponse;

/**
 * Action to send une predefined response
 */
class SendPredefinedResponseAction
{
    public function execute(
        Participant $senofr,
        PredefinedResponse $response,
        string $type,
        string $masterKey
    ): array {
        // Check that the réponse belongs to the same draw
        if ($response->draw_id !== $senofr->draw_id) {
            return [
                'success' => false,
                'error' => 'Invalid predefined response'
            ];
        }

        // Retrieve the content of the predefined response
        $content = $response->getDecryptedAttribute('response_encrypted', $masterKey);

        // Utiliser l'action d'envoi of message standard
        $sendAction = new SendMessageAction();
        return $sendAction->execute($senofr, $content, $type, $masterKey);
    }
}
