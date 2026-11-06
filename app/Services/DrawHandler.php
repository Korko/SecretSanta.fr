<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\ParticipantsCollection;
use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Notifications\TargetDrawn;
use Solver;

final class DrawHandler
{
    public static function solve(Draw $draw, ?ParticipantsCollection $participants = null): void
    {
        $hat = self::getHat($participants ?: $draw->participants);

        $participants = $draw->participants->pluck(null, 'id');
        foreach ($hat as $santaIdx => $targetIdx) {
            $participants[$santaIdx]->target()->save($participants[$targetIdx]);
        }

        $draw->next_solvable = self::canRedraw($draw->participants->fresh());
        $draw->save();

        $draw->participants->each(function ($participant): void {
            $participant->notify(new TargetDrawn);
        });
    }

    public static function getHat(ParticipantsCollection $participants): array
    {
        return Solver::one(
            $participants->pluck(null, 'id')->toArray(),
            $participants->pluck('exclusions.*.id', 'id')->toArray()
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
