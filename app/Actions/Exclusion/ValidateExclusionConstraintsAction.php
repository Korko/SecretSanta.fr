<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Draw;
use App\Services\Draw\ExclusionManager;
use Illuminate\Support\Facades\Log;

/**
 * Action pour valider les contraintes d'exclusion
 */
class ValidateExclusionConstraintsAction
{
    public function execute(Draw $draw): array
    {
        try {
            $participants = $draw->acceptedParticipants;
            $participantCount = $participants->count();

            if ($participantCount < 3) {
                return [
                    'success' => true,
                    'valid' => false,
                    'errors' => ['At least 3 participants are required']
                ];
            }

            $errors = [];
            $warnings = [];

            // Construire la matrice d'exclusions
            $exclusionManager = new ExclusionManager();
            $exclusionMatrix = $exclusionManager->buildExclusionMatrix($draw);

            // Vérifier chaque participant
            foreach ($participants as $participant) {
                $participantExclusions = $exclusionMatrix[$participant->id] ?? [];
                $strongExclusions = array_filter($participantExclusions, fn($type) => $type === 'strong');
                $weakExclusions = array_filter($participantExclusions, fn($type) => $type === 'weak');

                // Un participant ne peut pas avoir tous les autres exclus (fort)
                if (count($strongExclusions) >= $participantCount - 1) {
                    $errors[] = "Participant {$participant->uuid} has too many strong exclusions";
                }

                // Avertissement si beaucoup d'exclusions faibles
                if (count($weakExclusions) >= $participantCount - 2) {
                    $warnings[] = "Participant {$participant->uuid} has many weak exclusions that might be ignored";
                }
            }

            return [
                'success' => true,
                'valid' => empty($errors),
                'errors' => $errors,
                'warnings' => $warnings
            ];

        } catch (\Exception $e) {
            Log::error("Failed to validate exclusion constraints", [
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
