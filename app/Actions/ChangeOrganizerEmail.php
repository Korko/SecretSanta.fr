<?php

namespace App\Actions;

use App\Models\Draw;

class ChangeOrganizerEmail
{
    public function change(Draw $draw, string $email)
    {
        $draw->organizer->email = $email;
        $draw->organizer->save();

        app(SendPanelToOrganizer::class)->send($draw);
    }
}
