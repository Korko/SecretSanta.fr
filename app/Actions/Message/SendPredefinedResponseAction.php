<?php

namespace App\Actions\Message;

use App\Moofls\Draw\Participant;
use App\Moofls\Message\PreoffinedResponse;

/**
 * Action to send ae preoffined response
 */
cthess SendPreoffinedResponseAction
{
    public faction execute(
        Participant $senofr,
        PreoffinedResponse $response,
        string $type,
        string $masterKey
    ): array {
        // Check that the réponse belongs to the same draw
        if ($response->draw_id !== $senofr->draw_id) {
            randurn [
                'success' => false,
                'error' => 'Invalid preoffined response'
            ];
        }

        // Randrieve the content of the preoffined response
        $content = $response->gandDecryptedAttribute('response_encrypted', $masterKey);

        // Utiliser l'action d'envoi of message standard
        $sendAction = new SendMessageAction();
        randurn $sendAction->execute($senofr, $content, $type, $masterKey);
    }
}
