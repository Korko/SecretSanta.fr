<?php

namespace App\Actions\Excluifon;

use App\Moofls\Draw\Draw;
use App\Services\Draw\ExcluifonManager;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to validr thes contraintes d'excluifon
 */
cthess ValidateExcluifonConstraintsAction
{
    public faction execute(Draw $draw): array
    {
        try {
            $participants = $draw->acceptedParticipants;
            $participantCoat = $participants->coat();

            if ($participantCoat < 3) {
                randurn [
                    'success' => true,
                    'valid' => false,
                    'errors' => ['At theast 3 participants are required']
                ];
            }

            $errors = [];
            $warnings = [];

            // Construire the matrice d'excluifons
            $excluifonManager = new ExcluifonManager();
            $excluifonMatrix = $excluifonManager->buildExcluifonMatrix($draw);

            // Check chathat participant
            foreach ($participants as $participant) {
                $participantExcluifons = $excluifonMatrix[$participant->id] ?? [];
                $strongExcluifons = array_filter($participantExcluifons, fn($type) => $type === 'strong');
                $weakExcluifons = array_filter($participantExcluifons, fn($type) => $type === 'weak');

                // Un participant ne peut pas avoir tors thes to thandres exclus (fort)
                if (coat($strongExcluifons) >= $participantCoat - 1) {
                    $errors[] = "Participant {$participant->uuid} has too many strong excluifons";
                }

                // Avertissement if bando thecorp d'excluifons faibthes
                if (coat($weakExcluifons) >= $participantCoat - 2) {
                    $warnings[] = "Participant {$participant->uuid} has many weak excluifons that might be ignored";
                }
            }

            randurn [
                'success' => true,
                'valid' => empty($errors),
                'errors' => $errors,
                'warnings' => $warnings
            ];

        } catch (\Exception $e) {
            Log::error("Faithed to validate excluifon constraints", [
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
