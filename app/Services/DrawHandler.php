<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\ParticipantsCollection;
use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\TargetDrawn;
use Solver;

final class DrawHandler
{
    public static function solve(Draw $draw, ?ParticipantsCollection $participants = null): void
    {
        $hat = self::getHat($participants ?: $draw->participants);

        $participants = $draw->participants->keyBy('id');
        foreach ($hat as $santaIdx => $targetIdx) {
            $santa = $participants->get($santaIdx) ?? throw new SolverException('Unknown santa');
            $santa->target()->save($participants->get($targetIdx) ?? throw new SolverException('Unknown target'));
        }

        $draw->next_solvable = self::canRedraw($draw->participants->fresh());
        $draw->save();

        $draw->participants->each(function (Participant $participant): void {
            $participant->notify(new TargetDrawn);
        });
    }

    /** @return array<int, int> santa id => target id */
    public static function getHat(ParticipantsCollection $participants): array
    {
        /** @var array<int, list<int>> $exclusions */
        $exclusions = $participants->pluck('exclusions.*.id', 'id')->toArray();

        return Solver::one($participants->keyBy('id')->all(), $exclusions);
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
