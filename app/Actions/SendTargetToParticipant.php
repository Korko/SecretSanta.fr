<?php

namespace App\Actions;

use App\Models\Participant;
use App\Notifications\TargetDrawn;

class SendTargetToParticipant
{
    public function send(Participant $participant)
    {
        $participant->mail->markAsCreated();

        $participant->notify(new TargetDrawn);
    }
}
