<?php

declare(strict_types=1);

namespace App\Enums;

// Why a participant flagged their target email to the organizer (emailed, and shown on their panel)
enum ReportReason: string
{
    case NotMe = 'not_me';
    case NotParticipating = 'not_participating';
}
