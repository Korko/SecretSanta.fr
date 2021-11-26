<?php

namespace App\Console\Commands;

use App\Notifications\TargetDrawn;
use Arr;
use Illuminate\Console\Command;
use DrawCrypt;
use URLParser;

class FixParticipant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:participant {url : The URL received by one of the participants to write to their santa} {id : The participant id} {email : The correct email of the participant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix a participant email';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setCryptIVFromUrl($this->argument('url'));

        $draw = URLParser::parseByName('dearSanta', $this->argument('url'))->participant->draw;

        $participant = $draw->participants->find($this->argument('id'));
        $participant->email = $this->argument('email');
        $participant->find($this->argument('id'))->save();

        $participant->notify(new TargetDrawn);
        $this->info('Participant mail sent');
    }

    protected function setCryptIVFromUrl($url)
    {
        $hash = Arr::get(explode('#', $url, 2), 1);
        $key = base64_decode($hash);
        DrawCrypt::setIV($key);
    }
}
