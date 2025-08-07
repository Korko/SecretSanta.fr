<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\PredefinedResponse;

/**
 * Action to send a predefined response
 */
class SendPredefinedResponseAction
{
    public function execute(
        Participant $sender,
        PredefinedResponse $response,
        string $type,
        string $masterKey
    ): array {
        // Check that the response belongs to the same draw
        if ($response->draw_id !== $sender->draw_id) {
            return [
                'success' => false,
                'error' => 'Invalid predefined response'
            ];
        }

        // Retrieve the content of the predefined response
        $content = $response->getDecryptedAttribute('response_encrypted', $masterKey);

        // Use standard message sending action
        $sendAction = new SendMessageAction();
        return $sendAction->execute($sender, $content, $type, $masterKey);
    }
}
