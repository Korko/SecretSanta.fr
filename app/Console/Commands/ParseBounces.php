<?php

namespace App\Console\Commands;

use App\Jobs\ParseBounces as Job;
use Illuminate\Console\Command;

class ParseBounces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:parse-bounces';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Email Inbox for bounces';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        Job::dispatchSync();
    }
}
