<?php

namespace App\Actions;

use App\Models\DearTarget;
use App\Models\Mail;
use App\Models\Participant;
use App\Notifications\DearTarget as DearTargetNotification;

class SendMessageToTarget
{
    public function send(Participant $participant, string $type): DearTarget
    {
        $dearTarget = new DearTarget();
        $dearTarget->draw()->associate($participant->draw);
        $dearTarget->sender()->associate($participant);
        $dearTarget->mail_type = $type;
        $dearTarget->save();

        $dearTarget->target->notify(new DearTargetNotification($dearTarget));

        return $dearTarget;
    }

    public function resend(DearTarget $dearTarget)
    {
        $dearTarget->mail->delivery_status = Mail::STATE_CREATED;
        $dearTarget->mail->save();

        $dearTarget->target->notify(new DearTargetNotification($dearTarget));
    }
}
