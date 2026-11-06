<?php

namespace App\Collections;

use App\Models\Participant;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

final class ParticipantsCollection extends BaseCollection
{
    public function redrawables(): self
    {
        return $this->filter(fn ($participant) => $participant->redraw);
    }

    public function appendTargetToExclusions(): self
    {
        return (clone $this)->each(function (Participant $participant): void {
            $participant->exclusions->add($participant->target);
        });
    }
}
