<?php

namespace App\Notifications;

use App\Mail\DearTarget as DearTargetMailable;
use App\Models\DearTarget as DearTargetModel;
use App\Models\Participant;
use Illuminate\Notifications\Notification;

class DearTarget extends Notification
{
    protected $dearTarget;

    public function __construct(DearTargetModel $dearTarget)
    {
        $this->dearTarget = $dearTarget;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  \App\Models\Participant  $target
     * @return array
     */
    public function via(Participant $target)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\Participant  $target
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $target)
    {
        return (new DearTargetMailable($target, $this->dearTarget))
            ->to($target->routeNotificationFor('mail'));
    }
}
