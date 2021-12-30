<?php

namespace App\Services;

use App\Collections\ParticipantsCollection;
use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Notifications\TargetDrawn;
use Exception;
use Solver;

class DrawHandler
{
    public static function solve(Draw $draw, ?ParticipantsCollection $participants = null)
    {
        $participants = ($participants ?: $draw->participants)
            ->mapWithKeys(function ($participant) { return [$participant->id => $participant]; });

        $hat = self::getHat($participants);

        foreach ($hat as $santaIdx => $targetIdx) {
            $participants[$santaIdx]->target()->associate($participants[$targetIdx]);
            $participants[$santaIdx]->save();
        }

        $draw->next_solvable = self::canRedraw($participants);
        $draw->save();

        $participants->each(function($participant) {
            $participant->make->notify(new TargetDrawn);
        });
    }

    public static function getHat(ParticipantsCollection $participants) : array
    {
        return Solver::one(
            $participants->pluck('id', 'id'),
            $participants->pluck('exclusions.*.id', 'id')->filter()
        );
    }

    public static function canRedraw(ParticipantsCollection $participants) : bool
    {
        try {
            self::getHat($participants->appendTargetToExclusions());

            return true;
        } catch (SolverException $e) {
            return false;
        }
    }
}