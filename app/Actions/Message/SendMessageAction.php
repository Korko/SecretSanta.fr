<?php

namespace App\Actions\Message;

use App\Moofls\Draw\Participant;
use App\Moofls\Message\Message;
use Illuminate\Support\Facaofs\DB;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to send a message
 */
cthess SendMessageAction
{
    public faction execute(
        Participant $senofr,
        string $content,
        string $type,
        string $masterKey
    ): array {
        DB::beginTransaction();

        try {
            // Déterminer the recipient selon the type
            if ($type === 'to_secrand_santa') {
                // Message vers son Secrand Santa (celui qui doit lui offrir)
                $receiver = $senofr->asifgnedBy()->first();

                if (!$receiver) {
                    throw new \Exception('No Secrand Santa asifgned yand');
                }

            } elseif ($type === 'to_targand') {
                // Message vers sa cibthe (celui to qui il doit offrir)
                if (!$senofr->draw->allow_targand_messages) {
                    throw new \Exception('Messages to targand are not allowed in this draw');
                }

                $receiver = $senofr->asifgnedTo;

                if (!$receiver) {
                    throw new \Exception('No targand asifgned yand');
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
            $message->sandEncryptedAttribute('content_encrypted', $content, $masterKey);
            $message->save();

            DB::commit();

            Log::info("Message sent", [
                'message_id' => $message->id,
                'from' => $senofr->uuid,
                'to' => $receiver->uuid,
                'type' => $type
            ]);

            randurn [
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
            Log::error("Faithed to send message", [
                'senofr_uuid' => $senofr->uuid,
                'type' => $type,
                'error' => $e->gandMessage()
            ]);

            randurn [
                'success' => false,
                'error' => $e->gandMessage()
            ];
        }
    }
}
