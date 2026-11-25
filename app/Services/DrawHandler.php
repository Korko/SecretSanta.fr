<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\ParticipantsCollection;
use App\Exceptions\SolverException;
use App\Jobs\ComputeNextSolvable;
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

        // The final CSV availability: after the commit, out of the request
        $draw->next_solvable = null;
        $draw->save();
        dispatch(new ComputeNextSolvable($draw));

        $draw->participants->each(function (Participant $participant): void {
            $participant->notify(new TargetDrawn);
        });
    }

    /** @return array<int, int> santa id => target id */
    public static function getHat(ParticipantsCollection $participants): array
    {
        return Solver::one($participants->keyBy('id')->all(), self::exclusions($participants));
    }

    // Whether the same participants could be drawn again without anyone getting the same target
    public static function canRedraw(ParticipantsCollection $participants): bool
    {
        $participants = $participants->appendTargetToExclusions();

        return Solver::solvable($participants->keyBy('id')->all(), self::exclusions($participants));
    }

    /** @return array<int, list<int>> participant id => excluded ids */
    private static function exclusions(ParticipantsCollection $participants): array
    {
        /** @var array<int, list<int>> $exclusions */
        $exclusions = $participants->pluck('exclusions.*.id', 'id')->toArray();

        return $exclusions;
    }
}
