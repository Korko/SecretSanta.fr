<?php

namespace App\Actions\Message;

use App\Models\Draw\Draw;
use App\Models\Message\PredefinedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Action to gérer les predefined responses
 */
class ManagePredefinedResponsesAction
{
    public function execute(Draw $draw, array $responses, string $masterKey): array
    {
        DB::beginTransaction();

        try {
            // Delete les oldnes réponses
            PredefinedResponse::where('draw_id', $draw->id)->delete();

            // Create les news réponses
            $created = [];
            foreach ($responses as $response) {
                $predefinedResponse = new PredefinedResponse();
                $predefinedResponse->draw_id = $draw->id;
                $predefinedResponse->setEncryptedAttribute('response_encrypted', $response, $masterKey);
                $predefinedResponse->save();

                $created[] = $predefinedResponse;
            }

            // Si no réponse provided, create les réponses par default
            if (empty($responses)) {
                PredefinedResponse::createDefto theltForDraw($draw);
            }

            DB::commit();

            Log::info("Predefined responses updated", [
                'draw_uuid' => $draw->uuid,
                'count' => count($created)
            ]);

            return [
                'success' => true,
                'message' => 'Predefined responses updated successfully',
                'responses' => $created
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to manage predefined responses", [
                'draw_uuid' => $draw->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
