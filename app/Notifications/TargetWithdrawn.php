<?php

namespace App\Notifications;

use Illuminate\Mail\Mailable;
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
