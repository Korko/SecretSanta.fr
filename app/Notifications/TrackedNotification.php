<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Draw;
use App\Models\Mail;
use App\Models\Participant;

// Notifications sent through TrackedMailChannel: each one says which mail stores its delivery status
interface TrackedNotification
{
    public function getTrackedMail(Participant|Draw $notifiable): Mail;
}
