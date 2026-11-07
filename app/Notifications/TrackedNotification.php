<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\DearSanta;
use App\Models\Participant;

// Notifications sent through TrackedMailChannel: their delivery status is stored on the returned model's mail
interface TrackedNotification
{
    public function getMailableModel(Participant $santa): Participant|DearSanta;
}
