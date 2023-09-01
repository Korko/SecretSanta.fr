<?php

namespace App\Actions;

use App\Models\DearSanta;
use App\Models\Mail;
use App\Models\Participant;
use App\Notifications\DearSanta as DearSantaNotification;

class SendMessageToSanta
{
    public function send(Participant $participant, string $content): DearSanta
    {
        $dearSanta = new DearSanta();
        $dearSanta->draw()->associate($participant->draw);
        $dearSanta->sender()->associate($participant);
        $dearSanta->mail_body = $content;
        $dearSanta->save();

        $dearSanta->target->notify(new DearSantaNotification($dearSanta));

        return $dearSanta;
    }

    public function resend(DearSanta $dearSanta)
    {
        $dearSanta->mail->delivery_status = Mail::STATE_CREATED;
        $dearSanta->mail->save();

        $dearSanta->target->notify(new DearSantaNotification($dearSanta));
    }
}
