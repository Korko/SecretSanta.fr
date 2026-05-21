<?php

namespace App\Console\Commands;

use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Illuminate\Support\Facades\Notification;
use function Laravel\Prompts\error;

class FixOrganizer extends Command
{
    use ParsesDrawUrl;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:fix-organizer {url? : The URL received by one of the participants to write to their santa} {email? : The correct email of the organizer}';

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
        try {
            $draw = $this->findOrAskDrawFromUrl($this->argument('url'));
        } catch(\Exception $e) {
            error('Draw not found - ' . $e->getMessage());
            report($e);
            return;
        }

        $participantOrganizer = false;
        if ($draw->organizer->email === $draw->organizer_email) {
            $participantOrganizer = true;
        }

        if ($this->argument('email')) {
            $draw->organizer_email = $this->argument('email');
            $draw->save();
        }

        Notification::route('mail', [
            $draw->organizer_email => $draw->organizer_name,
        ])->notify(new OrganizerRecap($draw));
        $this->info('Organizer Recap sent');

        if ($participantOrganizer) {
            if ($this->argument('email')) {
                $draw->organizer->email = $this->argument('email');
                $draw->organizer->save();
            }

            $draw->organizer->notifyNow(new TargetDrawn);
            $this->info('Participant mail sent');
        }
    }
}
