<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CleanTrash as Job;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Clean Email Trash')]
#[Signature('secretsanta:clean-bounces')]
final class CleanBounces extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        dispatch_sync(new Job());
    }
}
