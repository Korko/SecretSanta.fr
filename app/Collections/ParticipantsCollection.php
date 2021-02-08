<?php

namespace App\Collections;

use App\Exceptions\SolverException;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Solver;

class ParticipantsCollection extends BaseCollection
{
    public function canRedraw() : bool
    {
        try {
            $this->getRedraw();

            return true;
        } catch (SolverException $e) {
            return false;
        }
    }

    public function getRedraw() : array
    {
        return $this->appendTargetToExclusions()->solve();
    }

    public function appendTargetToExclusions() : self
    {
        return (clone $this)->each(function (Participant $participant) {
            $participant->exclusions->add($participant->target);
        });
    }

    public function solve() : array
    {
        return Solver::one($this->pluck(null, 'id')->toArray(), $this->pluck('exclusions.*.id', 'id')->toArray());
    }
}