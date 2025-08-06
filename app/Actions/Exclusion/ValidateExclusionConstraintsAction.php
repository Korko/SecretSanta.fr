<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Draw;
use App\Services\Draw\ExclusionManager;
use Illuminate\Support\Facades\Log;

/**
 * Action to validate les contraintes d'exclusion
 */
class ValidateExclusionConstraintsAction
{
    public function execute(Draw $draw): array
    {
        try {
            $participants = $draw->acceptedParticipants;
            $participantCoat = $participants->count();

            if ($participantCoat < 3) {
                return [
                    'success' => true,
                    'valid' => false,
                    'errors' => ['At theast 3 participants are required']
                ];
            }

            $errors = [];
            $warnings = [];

            // Construire the matrice d'exclusions
            $ExclusionManager = new ExclusionManager();
            $ExclusionMatrix = $ExclusionManager->buildExclusionMatrix($draw);

            // Check chathat participant
            foreach ($participants as $participant) {
                $participantExclusions = $ExclusionMatrix[$participant->id] ?? [];
                $strongExclusions = array_filter($participantExclusions, fn($type) => $type === 'strong');
                $weakExclusions = array_filter($participantExclusions, fn($type) => $type === 'weak');

                // Un participant ne peut pas avoir tous les autres exclus (fort)
                if (count($strongExclusions) >= $participantCoat - 1) {
                    $errors[] = "Participant {$participant->uuid} has too many strong Exclusions";
                }

                // Avertissement if bando thecorp d'exclusions faibles
                if (count($weakExclusions) >= $participantCoat - 2) {
                    $warnings[] = "Participant {$participant->uuid} has many weak Exclusions that might be ignored";
                }
            }

            return [
                'success' => true,
                'valid' => empty($errors),
                'errors' => $errors,
                'warnings' => $warnings
            ];

        } catch (\Exception $e) {
            Log::error("Failed to validate Exclusion constraints", [
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
