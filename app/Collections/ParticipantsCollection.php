<?php

namespace App\Collections;

use App\Exceptions\SolverException;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Solver;

class ParticipantsCollection extends BaseCollection
{
    public function redrawables() : self
    {
        return $this->filter(function ($participant) {
            return $participant->redraw;
        });
    }

    public function appendTargetToExclusions() : self
    {
        return (clone $this)->each(function (Participant $participant) {
            $participant->exclusions->add($participant->target);
        });
    }
}