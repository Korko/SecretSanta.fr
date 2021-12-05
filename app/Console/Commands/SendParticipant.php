<?php

namespace App\Console\Commands;

use App\Models\Participant;
use App\Notifications\TargetDrawn;

class SendParticipant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:participant {participant : ID of the participant} {url : The URL received by one of the participants to write to their santa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send again an email to a participant';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setCryptIVFromUrl($this->argument('url'));

        $participant = Participant::find($this->argument('participant'));

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');
    }
}
