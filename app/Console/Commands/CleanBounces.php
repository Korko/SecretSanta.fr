<?php

namespace App\Console\Commands;

use App\Jobs\CleanTrash as Job;
use Illuminate\Console\Command;

class CleanBounces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:clean-bounces';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean Email Trash';

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
