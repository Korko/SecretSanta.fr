<?php

namespace App\Actions;

use App\Models\Draw;

class ChangeOrganizerEmail
{
    public function change(Draw $draw, string $email)
    {
        $draw->organizer_email = $email;
        $draw->save();

        app(SendPanelToOrganizer::class)->send($draw);
    }
}
