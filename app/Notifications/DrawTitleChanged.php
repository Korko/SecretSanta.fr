<?php

namespace App\Notifications;

use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class DrawTitleChanged extends Notification
{
    public function __construct(
        protected readonly Draw $draw
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(Participant $participant): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toArray(Participant $participant): array
    {
        return [
            'old_title' => $this->draw->getOriginal('title'),
            'title' => $this->draw->title,
        ];
    }
}
