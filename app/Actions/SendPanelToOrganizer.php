<?php

namespace App\Actions;

use App\Models\Draw;
use App\Notifications\OrganizerRecap;

class SendPanelToOrganizer
{
    public function send(Draw $draw)
    {
        $draw->organizer->notify(new OrganizerRecap($draw));
    }
}
