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
        $participants = ($participants ?: $draw->participants)
            ->mapWithKeys(function ($participant) {
                return [$participant->id => $participant];
            });

        $hat = self::getHat($participants);

        foreach ($hat as $santaIdx => $targetIdx) {
            $participants[$santaIdx]->target()->associate($participants[$targetIdx]);
            $participants[$santaIdx]->save();
        }

        $draw->next_solvable = self::canRedraw($participants);
        $draw->save();

        $participants->each(function ($participant) {
            $participant->notify(new TargetDrawn);
        });
    }

    public static function getHat(ParticipantsCollection $participants): array
    {
        return Solver::one(
            $participants->pluck('id', 'id'),
            $participants
                ->pluck('exclusions.*.id', 'id')
                // Remove empty exclusions lists (or lists with only empty values)
                ->map(fn ($exclusion) => array_filter((array) $exclusion))
                ->filter(fn ($exclusion) => ! empty($exclusion))
        );
    }

    public static function canRedraw(ParticipantsCollection $participants): bool
    {
        try {
            self::getHat($participants->appendTargetToExclusions());

            return true;
        } catch (SolverException) {
            return false;
        }
    }
}
