<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ParseBounces as Job;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Check Email Inbox for bounces')]
#[Signature('secretsanta:parse-bounces')]
final class ParseBounces extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        dispatch_sync(new Job());
    }
}
