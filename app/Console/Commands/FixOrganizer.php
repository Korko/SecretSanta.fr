<?php

namespace App\Console\Commands;

use App\Actions\ChangeOrganizerEmail;
use App\Actions\SendPanelToOrganizer;
use URLParser;

class FixOrganizer extends Command
{
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
        $this->setCryptIVFromUrl($this->argument('url'));

        $draw = URLParser::parseByName('dearSanta', $this->argument('url'))->participant->draw;

        if ($this->argument('email')) {
            app(ChangeOrganizerEmail::class)->change($draw, $this->argument('email'));
        } else {
            app(SendPanelToOrganizer::class)->send($draw);
        }

        $this->info('Organizer Recap sent');
    }
}
