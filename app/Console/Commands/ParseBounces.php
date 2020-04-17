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
    protected $signature = 'bounces:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Email Inbox for bounces';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Job::dispatchNow();
    }
}
