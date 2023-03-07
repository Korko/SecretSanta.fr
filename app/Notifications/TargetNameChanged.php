<?php

namespace App\Notifications;

use Illuminate\Mail\Mailable;
use App\Mail\TargetNameChanged as TargetNameChangedMailable;
use App\Models\Participant;
use Illuminate\Notifications\Notification;

class TargetNameChanged extends Notification
{
    protected $target;

    public function __construct(Participant $target)
    {
        $this->target = $target;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  \App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa): Mailable
    {
        return (new TargetNameChangedMailable($santa, $target))
            ->to($santa->routeNotificationFor('mail'));
    }
}
