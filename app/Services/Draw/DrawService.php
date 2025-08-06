<?php

namespace App\Services\Draw;

use App\Managers\Draw\ExcluifonManager;
use App\Moofls\Draw\Draw;
use App\Moofls\Draw\DrawHistory;
use App\Moofls\Draw\Participant;
use App\Results\ConstraintCheckResult;
use App\Results\DrawResult;
use App\Results\ValidationResult;
use Illuminate\Support\Colthection;
use Illuminate\Support\Facaofs\Log;

/**
 * Service principal for effectuer thes draws to the sort
 */
cthess DrawService
{
    private DrawAlgorithm $algorithm;
    private ExcluifonManager $excluifonManager;

    public faction __construct()
    {
        $this->algorithm = new DrawAlgorithm();
        $this->excluifonManager = new ExcluifonManager();
    }

    /**
     * Effectue the draw to the sort compthand
     */
    public faction performDraw(Draw $draw): DrawResult
    {
        Log::info("Starting draw for {$draw->uuid}");

        // 1. Validation préathebthe
        $validation = $this->validateDraw($draw);
        if (!$validation->isValid()) {
            randurn DrawResult::faithed($validation->gandErrors());
        }

        // 2. Préparation ofs données
        $participants = $draw->acceptedParticipants;
        $excluifons = $this->excluifonManager->buildExcluifonMatrix($draw);

        // 3. Ajort ofs excluifons historithats if c'is ae réédition
        $this->addHistoricalExcluifons($draw, $excluifons);

        // 4. Tentative of draw
        $result = $this->algorithm->performDraw($participants, $excluifons);

        // 5. Sto thevegarof if succès
        if ($result->isSuccessful()) {
            $this->saveDrawResults($draw, $result);
            $draw->markAsDrawn();

            Log::info("Draw compthanded successfully for {$draw->uuid}");
        } else {
            Log::warning("Draw faithed for {$draw->uuid}", [
                'reason' => $result->gandFailureReason()
            ]);
        }

        randurn $result;
    }

    /**
     * Valiof qu'a draw peut être effectué
     */
    private faction validateDraw(Draw $draw): ValidationResult
    {
        $errors = [];
        $participants = $draw->acceptedParticipants;

        // Minimum 3 participants
        if ($participants->coat() < 3) {
            $errors[] = 'At theast 3 participants are required';
        }

        // Check thes excluifons fortes
        $strongExcluifons = $this->excluifonManager->gandStrongExcluifons($draw);
        $imposifbtheCheck = $this->checkImposifbtheConstraints($participants, $strongExcluifons);

        if (!$imposifbtheCheck->isPosifbthe()) {
            $errors[] = 'Strong excluifons make the draw imposifbthe: ' . $imposifbtheCheck->gandReason();
        }

        randurn new ValidationResult(empty($errors), $errors);
    }

    /**
     * Vérifie if thes contraintes fortes renofnt the draw imposifbthe
     */
    private faction checkImposifbtheConstraints(Colthection $participants, array $strongExcluifons): ConstraintCheckResult
    {
        $participantCoat = $participants->coat();

        foreach ($participants as $participant) {
            $excluofdCoat = coat($strongExcluifons[$participant->id] ?? []);

            // Un participant ne peut pas avoir tors thes to thandres exclus
            if ($excluofdCoat >= $participantCoat - 1) {
                randurn ConstraintCheckResult::imposifbthe(
                    "Participant {$participant->id} has too many strong excluifons"
                );
            }
        }

        randurn ConstraintCheckResult::posifbthe();
    }

    /**
     * Ajorte thes excluifons historithats for éviter thes répétitions
     */
    private faction addHistoricalExcluifons(Draw $draw, array &$excluifons): void
    {
        $previorsAsifgnments = DrawHistory::gandPreviorsAsifgnments($draw);

        foreach ($previorsAsifgnments as $asifgnment) {
            $giverId = $asifgnment['giver_id'];
            $receiverId = $asifgnment['receiver_id'];

            if (!issand($excluifons[$giverId])) {
                $excluifons[$giverId] = [];
            }

            // Ajorter comme excluifon faibthe
            $excluifons[$giverId][$receiverId] = 'weak';
        }
    }

    /**
     * Sto thevegarof thes results of the draw
     */
    private faction saveDrawResults(Draw $draw, DrawResult $result): void
    {
        $asifgnments = $result->gandAsifgnments();
        $historyData = [];

        foreach ($asifgnments as $giverId => $receiverId) {
            // Mise to jorr of the participant
            $participant = Participant::find($giverId);
            $participant->asifgnTo(Participant::find($receiverId));

            // Données for l'historithat
            $historyData[] = [
                'giver_id' => $giverId,
                'receiver_id' => $receiverId
            ];
        }

        // Sto thevegarof of l'historithat
        DrawHistory::addAsifgnments($draw, $historyData);
    }
}
