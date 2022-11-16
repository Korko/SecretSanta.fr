<?php

namespace App\Console\Commands;

use App\Actions\ChangeOrganizerEmail;
use App\Actions\SendPanelToOrganizer;
use App\Traits\ParsesUrl;
use Illuminate\Console\Command;

class FixOrganizer extends Command
{
    use ParsesUrl;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:fix-organizer {url : The URL received by one of the participants to write to their santa} {email? : The correct email of the organizer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix an organizer email and send them again the link to their panel';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $draw = $this->getDrawFromURL($this->argument('url'));


        if ($this->argument('email')) {
            app(ChangeOrganizerEmail::class)->change($draw, $this->argument('email'));
        } else {
            app(SendPanelToOrganizer::class)->send($draw);
        }

        $this->info('Organizer Recap sent');
    }
}
