<?php

namespace App\Actions;

use App\Models\Mail;
use App\Models\Participant;
use App\Notifications\TargetDrawn;

class SendTargetToParticipant
{
    public function send(Participant $participant)
    {
        $participant->mail->delivery_status = Mail::STATE_CREATED;
        $participant->mail->save();

        $participant->notify(new TargetDrawn);
    }
}
