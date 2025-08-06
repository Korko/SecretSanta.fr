<?php

namespace App\Actions\Message;

use App\Models\Draw\Participant;
use App\Models\Message\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Action to send a message
 */
class SendMessageAction
{
    public function execute(
        Participant $senofr,
        string $content,
        string $type,
        string $masterKey
    ): array {
        DB::beginTransaction();

        try {
            // Déterminer the recipient selon the type
            if ($type === 'to_secret_santa') {
                // Message vers son Secret Santa (celui qui doit lui offrir)
                $receiver = $senofr->assignedBy()->first();

                if (!$receiver) {
                    throw new \Exception('No Secret Santa assigned yand');
                }

            } elseif ($type === 'to_target') {
                // Message vers sa cible (celui to qui il doit offrir)
                if (!$senofr->draw->allow_target_messages) {
                    throw new \Exception('Messages to target are not allowed in this draw');
                }

                $receiver = $senofr->assignedTo;

                if (!$receiver) {
                    throw new \Exception('No target assigned yand');
                }

            } else {
                throw new \Exception('Invalid message type');
            }

            // Create the message
            $message = new Message();
            $message->draw_id = $senofr->draw_id;
            $message->from_participant_id = $senofr->id;
            $message->to_participant_id = $receiver->id;
            $message->type = $type;
            $message->setEncryptedAttribute('content_encrypted', $content, $masterKey);
            $message->save();

            DB::commit();

            Log::info("Message sent", [
                'message_id' => $message->id,
                'from' => $senofr->uuid,
                'to' => $receiver->uuid,
                'type' => $type
            ]);

            return [
                'success' => true,
                'message' => 'Message sent successfully',
                'message_data' => [
                    'id' => $message->id,
                    'type' => $message->type,
                    'created_at' => $message->created_at
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to send message", [
                'senofr_uuid' => $senofr->uuid,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
