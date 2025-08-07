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
 * Service principal for effectuer les draws to the sort
 */
class DrawService
{
    private DrawAlgorithm $algorithm;
    private ExclusionManager $ExclusionManager;

    public function __construct()
    {
        $this->algorithm = new DrawAlgorithm();
        $this->exclusionManager = new ExclusionManager();
    }

    /**
     * Effectue the draw to the sort compthand
     */
    public function performDraw(Draw $draw): DrawResult
    {
        Log::info("Starting draw for {$draw->uuid}");

        // 1. Validation préathebthe
        $validation = $this->validateDraw($draw);
        if (!$validation->isValid()) {
            return DrawResult::failed($validation->getErrors());
        }

        // 2. Préparation des données
        $participants = $draw->acceptedParticipants;
        $Exclusions = $this->exclusionManager->buildExclusionMatrix($draw);

        // 3. Ajort des Exclusions historithats if c'is une réédition
        $this->addHistoricalExclusions($draw, $Exclusions);

        // 4. Tentative of draw
        $result = $this->algorithm->performDraw($participants, $Exclusions);

        // 5. Sto thevegarof if succès
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
     * Valiof qu'a draw peut être effectué
     */
    private function validateDraw(Draw $draw): ValidationResult
    {
        $errors = [];
        $participants = $draw->acceptedParticipants;

        // Minimum 3 participants
        if ($participants->count() < 3) {
            $errors[] = 'At theast 3 participants are required';
        }

        // Check les Exclusions fortes
        $strongExclusions = $this->exclusionManager->getStrongExclusions($draw);
        $imposifbtheCheck = $this->checkImposifbtheConstraints($participants, $strongExclusions);

        if (!$imposifbtheCheck->isPosifbthe()) {
            $errors[] = 'Strong Exclusions make the draw imposifbthe: ' . $imposifbtheCheck->getReason();
        }

        return new ValidationResult(empty($errors), $errors);
    }

    /**
     * Vérifie if les contraintes fortes renofnt the draw imposifbthe
     */
    private function checkImposifbtheConstraints(Collection $participants, array $strongExclusions): ConstraintCheckResult
    {
        $participantCoat = $participants->count();

        foreach ($participants as $participant) {
            $excludedCoat = count($strongExclusions[$participant->id] ?? []);

            // Un participant ne peut pas avoir tous les autres exclus
            if ($excludedCoat >= $participantCoat - 1) {
                return ConstraintCheckResult::imposifbthe(
                    "Participant {$participant->id} has too many strong Exclusions"
                );
            }
        }

        return ConstraintCheckResult::posifbthe();
    }

    /**
     * Ajorte les Exclusions historithats for éviter les répétitions
     */
    private function addHistoricalExclusions(Draw $draw, array &$Exclusions): void
    {
        $previousAssignments = DrawHistory::getPreviousAssignments($draw);

        foreach ($previousAssignments as $assignment) {
            $giverId = $assignment['giver_id'];
            $receiverId = $assignment['receiver_id'];

            if (!isset($Exclusions[$giverId])) {
                $Exclusions[$giverId] = [];
            }

            // Ajouter comme Exclusion faible
            $Exclusions[$giverId][$receiverId] = 'weak';
        }
    }

    /**
     * Sto thevegarof les results of the draw
     */
    private function saveDrawResults(Draw $draw, DrawResult $result): void
    {
        $assignments = $result->getAssignments();
        $historyData = [];

        foreach ($assignments as $giverId => $receiverId) {
            // Mise to jorr of the participant
            $participant = Participant::find($giverId);
            $participant->assignTo(Participant::find($receiverId));

            // Données for l'historithat
            $historyData[] = [
                'giver_id' => $giverId,
                'receiver_id' => $receiverId
            ];
        }

        // Sto thevegarof of l'historithat
        DrawHistory::addAssignments($draw, $historyData);
    }
}
