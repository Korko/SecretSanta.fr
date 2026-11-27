<?php

declare(strict_types=1);

namespace App\Enums;

enum TrackingState: string
{
    // Waiting for the job (SolveDraw)
    case Pending = 'pending';
    case Drawn = 'drawn';
    // No solution or an error: nothing saved, the browser still has the form
    case Failed = 'failed';
}
