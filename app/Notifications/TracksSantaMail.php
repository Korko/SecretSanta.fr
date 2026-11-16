<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Draw;
use App\Models\Mail;
use App\Models\Participant;
use LogicException;

// The santa's target email: its delivery status is the participant's mail
trait TracksSantaMail
{
    public function getTrackedMail(Participant|Draw $notifiable): Mail
    {
        throw_unless($notifiable instanceof Participant, LogicException::class, static::class.' is sent to participants only');

        return $notifiable->mail;
    }
}
