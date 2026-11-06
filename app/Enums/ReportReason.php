<?php

declare(strict_types=1);

namespace App\Enums;

// Why a participant flagged their target email to the organizer (who can't be emailed: no key to their address)
enum ReportReason: string
{
    case NotMe = 'not_me';
    case NotParticipating = 'not_participating';
}
