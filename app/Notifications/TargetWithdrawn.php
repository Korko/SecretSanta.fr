<?php

namespace App\Notifications;

use App\Mail\TargetWithdrawn as TargetWithdrawnMailable;
use App\Models\Participant;
use Illuminate\Notifications\Notification;

class TargetWithdrawn extends Notification
{
    protected $oldTarget;

    protected $newTarget;

    public function __construct(Participant $oldTarget, Participant $newTarget)
    {
        $this->oldTarget = $oldTarget;
        $this->newTarget = $newTarget;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  \App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa)
    {
        return (new TargetWithdrawnMailable($santa, $this->oldTarget, $this->newTarget))
            ->to($santa->routeNotificationFor('mail'));
    }
}
