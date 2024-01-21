<?php

namespace App\Notifications;

use App\Mail\TargetWithdrawn as TargetWithdrawnMailable;
use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class TargetWithdrawn extends Notification
{
    public function __construct(
        protected readonly Participant $oldTarget,
        protected readonly Participant $newTarget
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $santa): Mailable
    {
        return (new TargetWithdrawnMailable($santa, $this->oldTarget, $this->newTarget))
            ->to($santa->routeNotificationFor('mail'));
    }
}
