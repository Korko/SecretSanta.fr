<?php

declare(strict_types=1);

namespace App\Console\Commands;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\warning;

// Before changing a draw or emailing people with no organizer in the loop: "no" unless answered otherwise,
// also without a terminal (cron, pipe). The command needs {--force} for scripts
trait ConfirmsChanges
{
    protected function confirmOrForce(string $question): bool
    {
        if ((bool) $this->option('force') || confirm($question, default: false)) {
            return true;
        }

        warning('Cancelled: nothing changed, no email sent');

        return false;
    }
}
