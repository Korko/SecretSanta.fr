<?php

namespace App\Services;

use App\Collections\ParticipantsCollection;
use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Notifications\TargetDrawn;
use Solver;

class DrawHandler
{
    public static function solve(Draw $draw, ?ParticipantsCollection $participants = null)
    {
        $hat = self::getHat($participants ?: $draw->participants);

        $participants = $draw->participants->pluck(null, 'id');
        foreach ($hat as $santaIdx => $targetIdx) {
            $participants[$santaIdx]->target()->save($participants[$targetIdx]);
        }

        $draw->participants->each->notify(new TargetDrawn);

        $draw->next_solvable = self::canRedraw($draw->participants->fresh());
        $draw->save();
    }

    public static function getHat(ParticipantsCollection $participants) : array
    {
        return Solver::one(
            $participants->pluck(null, 'id')->toArray(),
            $participants->pluck('exclusions.*.id', 'id')->toArray()
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