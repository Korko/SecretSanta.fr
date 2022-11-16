<?php

namespace App\Console\Commands;

use App\Actions\ChangeParticipantEmail;
use App\Actions\SendTargetToParticipant;
use App\Traits\ParsesUrl;
use Illuminate\Console\Command;

class FixParticipant extends Command
{
    use ParsesUrl;

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
    public function handle()
    {
        $draw = $this->getDrawFromURL($this->argument('url'));


        $participant = $draw->participants->find($this->argument('id'));

        if ($this->argument('email')) {
            app(ChangeParticipantEmail::class)->change($participant, $this->argument('email'));
        } else {
            app(SendTargetToParticipant::class)->send($participant);
        }

        $this->info('Participant mail sent');
    }
}
