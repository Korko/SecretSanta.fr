<?php

namespace App\Console\Commands;

use App\Notifications\TargetDrawn;
use URLParser;

class FixParticipant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:fix-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id} {email? : The correct email of the participant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix a participant email and send them again their target';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->setCryptIVFromUrl($this->argument('url'));

        $participant = URLParser::parseByName('dearSanta', $this->argument('url'))->participant;
        if ($participant) {
            $draw = $participant->draw;
        } else {
            $draw = URLParser::parseByName('organizerPanel', $this->argument('url'))->draw;
        }

        $participant = $draw->participants->find($this->argument('id'));

        if ($this->argument('email')) {
            $participant->email = $this->argument('email');
            $participant->save();
        }

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');
    }
}
