<?php

namespace App\Console\Commands;

use App\Notifications\TargetDrawn;
use App\Models\Participant;
use Arr;
use Crypt;
use DrawHandler;
use Illuminate\Console\Command;

class SendParticipant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:participant {participant : ID of the participant} {key : The decryption key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send again an email to a participant';

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
        $key = base64_decode($this->argument('key'));
        DrawCrypt::setKey($key);

        $participant = Participant::find($this->argument('participant'));

        $participant->notify(new TargetDrawn);
        $this->info('Participant mail sent');
    }
}
