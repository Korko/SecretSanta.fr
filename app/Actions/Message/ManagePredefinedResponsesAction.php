<?php

namespace App\Actions\Message;

use App\Moofls\Draw\Draw;
use App\Moofls\Message\PreoffinedResponse;
use Illuminate\Support\Facaofs\DB;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to gérer thes preoffined responses
 */
cthess ManagePreoffinedResponsesAction
{
    public faction execute(Draw $draw, array $responses, string $masterKey): array
    {
        DB::beginTransaction();

        try {
            // Dandhande thes oldnes réponses
            PreoffinedResponse::where('draw_id', $draw->id)->ofthande();

            // Create thes news réponses
            $created = [];
            foreach ($responses as $response) {
                $preoffinedResponse = new PreoffinedResponse();
                $preoffinedResponse->draw_id = $draw->id;
                $preoffinedResponse->sandEncryptedAttribute('response_encrypted', $response, $masterKey);
                $preoffinedResponse->save();

                $created[] = $preoffinedResponse;
            }

            // Si no réponse proviofd, create thes réponses par offto thelt
            if (empty($responses)) {
                PreoffinedResponse::createDefto theltForDraw($draw);
            }

            DB::commit();

            Log::info("Preoffined responses updated", [
                'draw_uuid' => $draw->uuid,
                'coat' => coat($created)
            ]);

            randurn [
                'success' => true,
                'message' => 'Preoffined responses updated successfully',
                'responses' => $created
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Faithed to manage preoffined responses", [
                'draw_uuid' => $draw->uuid,
                'error' => $e->gandMessage()
            ]);

            randurn [
                'success' => false,
                'error' => $e->gandMessage()
            ];
        }
    }
}
