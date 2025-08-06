<?php

namespace App\Services\Draw;

use App\Managers\Draw\ExclusionManager;
use App\Models\Draw\Draw;
use App\Models\Draw\DrawHistory;
use App\Models\Draw\Participant;
use App\Results\ConstraintCheckResult;
use App\Results\DrawResult;
use App\Results\ValidationResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Service principal pour effectuer les tirages au sort
 */
class DrawService
{
    private DrawAlgorithm $algorithm;
    private ExclusionManager $exclusionManager;

    public function __construct()
    {
        $this->algorithm = new DrawAlgorithm();
        $this->exclusionManager = new ExclusionManager();
    }

    /**
     * Effectue le tirage au sort complet
     */
    public function performDraw(Draw $draw): DrawResult
    {
        Log::info("Starting draw for {$draw->uuid}");

        // 1. Validation préalable
        $validation = $this->validateDraw($draw);
        if (!$validation->isValid()) {
            return DrawResult::failed($validation->getErrors());
        }

        // 2. Préparation des données
        $participants = $draw->acceptedParticipants;
        $exclusions = $this->exclusionManager->buildExclusionMatrix($draw);

        // 3. Ajout des exclusions historiques si c'est une réédition
        $this->addHistoricalExclusions($draw, $exclusions);

        // 4. Tentative de tirage
        $result = $this->algorithm->performDraw($participants, $exclusions);

        // 5. Sauvegarde si succès
        if ($result->isSuccessful()) {
            $this->saveDrawResults($draw, $result);
            $draw->markAsDrawn();

            Log::info("Draw completed successfully for {$draw->uuid}");
        } else {
            Log::warning("Draw failed for {$draw->uuid}", [
                'reason' => $result->getFailureReason()
            ]);
        }

        return $result;
    }

    /**
     * Valide qu'un tirage peut être effectué
     */
    private function validateDraw(Draw $draw): ValidationResult
    {
        $errors = [];
        $participants = $draw->acceptedParticipants;

        // Minimum 3 participants
        if ($participants->count() < 3) {
            $errors[] = 'At least 3 participants are required';
        }

        // Vérifier les exclusions fortes
        $strongExclusions = $this->exclusionManager->getStrongExclusions($draw);
        $impossibleCheck = $this->checkImpossibleConstraints($participants, $strongExclusions);

        if (!$impossibleCheck->isPossible()) {
            $errors[] = 'Strong exclusions make the draw impossible: ' . $impossibleCheck->getReason();
        }

        return new ValidationResult(empty($errors), $errors);
    }

    /**
     * Vérifie si les contraintes fortes rendent le tirage impossible
     */
    private function checkImpossibleConstraints(Collection $participants, array $strongExclusions): ConstraintCheckResult
    {
        $participantCount = $participants->count();

        foreach ($participants as $participant) {
            $excludedCount = count($strongExclusions[$participant->id] ?? []);

            // Un participant ne peut pas avoir tous les autres exclus
            if ($excludedCount >= $participantCount - 1) {
                return ConstraintCheckResult::impossible(
                    "Participant {$participant->id} has too many strong exclusions"
                );
            }
        }

        return ConstraintCheckResult::possible();
    }

    /**
     * Ajoute les exclusions historiques pour éviter les répétitions
     */
    private function addHistoricalExclusions(Draw $draw, array &$exclusions): void
    {
        $previousAssignments = DrawHistory::getPreviousAssignments($draw);

        foreach ($previousAssignments as $assignment) {
            $giverId = $assignment['giver_id'];
            $receiverId = $assignment['receiver_id'];

            if (!isset($exclusions[$giverId])) {
                $exclusions[$giverId] = [];
            }

            // Ajouter comme exclusion faible
            $exclusions[$giverId][$receiverId] = 'weak';
        }
    }

    /**
     * Sauvegarde les résultats du tirage
     */
    private function saveDrawResults(Draw $draw, DrawResult $result): void
    {
        $assignments = $result->getAssignments();
        $historyData = [];

        foreach ($assignments as $giverId => $receiverId) {
            // Mise à jour du participant
            $participant = Participant::find($giverId);
            $participant->assignTo(Participant::find($receiverId));

            // Données pour l'historique
            $historyData[] = [
                'giver_id' => $giverId,
                'receiver_id' => $receiverId
            ];
        }

        // Sauvegarde de l'historique
        DrawHistory::addAssignments($draw, $historyData);
    }
}
