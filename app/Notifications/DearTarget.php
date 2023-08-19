<?php

namespace App\Notifications;

use App\Mail\DearTarget as DearTargetMailable;
use App\Models\DearTarget as DearTargetModel;
use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class DearTarget extends Notification
{
    public function __construct(
        protected readonly DearTargetModel $dearTarget
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(Participant $target): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $target): Mailable
    {
        return (new DearTargetMailable($target, $this->dearTarget))
            ->to($target->routeNotificationFor('mail'));
    }
}
