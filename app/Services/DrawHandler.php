<?php

namespace App\Services;

use App\Collections\ParticipantsCollection;
use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Notifications\TargetDrawn;
use Solver;

class DrawHandler
{
    public static function solve(Draw $draw, ?ParticipantsCollection $santas = null)
    {
        $santas = ($santas ?: $draw->santas)
            ->loadMissing(['exclusions'])
            ->mapWithKeys(function ($participant) use ($draw) {
                return [$participant->id => $participant->setRelation('draw', $draw)];
            });

        $hat = self::getHat($santas);

        foreach ($hat as $santaIdx => $targetIdx) {
            $santas[$santaIdx]->target()->associate($santas[$targetIdx]);
            $santas[$santaIdx]->save();
        }

        $draw->save();

        $santas->each(function ($participant) {
            $participant->notify(new TargetDrawn);
        });
    }

    public static function getHat(ParticipantsCollection $santas): array
    {
        return Solver::one(
            $santas->pluck('id', 'id'),
            $santas
                ->pluck('exclusions.*.id', 'id')
                // Remove empty exclusions lists (or lists with only empty values)
                ->map(fn ($exclusion) => array_filter((array) $exclusion))
                ->filter(fn ($exclusion) => ! empty($exclusion))
        );
    }

    public static function canRedraw(ParticipantsCollection $santas): bool
    {
        try {
            self::getHat($santas->appendTargetToExclusions());

            return true;
        } catch (SolverException) {
            return false;
        }
    }
}
