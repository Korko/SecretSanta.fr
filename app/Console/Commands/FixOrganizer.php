<?php

namespace App\Console\Commands;

use App\Actions\ChangeOrganizerEmail;
use App\Actions\ChangeParticipantEmail;
use App\Actions\SendPanelToOrganizer;
use App\Actions\SendTargetToParticipant;
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
     */
    public function handle(): void
    {
        $draw = $this->getDrawFromURL($this->argument('url'));

        if ($this->argument('email')) {
            app(ChangeOrganizerEmail::class)->change($draw, $this->argument('email'));
        } else {
            app(SendPanelToOrganizer::class)->send($draw);
        }

        $this->info('Organizer Recap sent');

        if ($draw->participant_organizer) {
            if ($this->argument('email')) {
                app(ChangeParticipantEmail::class)->change($draw->organizer, $this->argument('email'));
            } else {
                app(SendTargetToParticipant::class)->send($draw->organizer);
            }

            $this->info('Participant mail sent');
        }
    }
}
