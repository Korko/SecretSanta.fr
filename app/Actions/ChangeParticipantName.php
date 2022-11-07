<?php

namespace App\Actions;

use App\Models\Participant;
use App\Notifications\TargetNameChanged;

class ChangeParticipantName
{
    public function change(Participant $participant, string $name)
    {
        $participant->name = $name;
        $participant->save();

        $participant->santa->notify(new TargetNameChanged($participant));
    }
}
