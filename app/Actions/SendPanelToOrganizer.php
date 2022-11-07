<?php

namespace App\Actions;

use App\Models\Draw;
use App\Notifications\OrganizerRecap;
use Illuminate\Support\Facades\Notification;

class SendPanelToOrganizer
{
    public function send(Draw $draw)
    {
        Notification::route('mail', [
            $draw->organizer_email => $draw->organizer_name,
        ])->notify(new OrganizerRecap($draw));
    }
}
