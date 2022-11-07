<?php

namespace App\Actions;

use App\Models\Participant;

class ChangeParticipantEmail
{
    public function change(Participant $participant, string $email)
    {
        $participant->email = $email;
        $participant->save();

        app(SendTargetToParticipant::class)->send($participant);
    }
}
