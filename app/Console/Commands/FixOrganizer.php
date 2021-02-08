<?php

namespace App\Console\Commands;

use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Arr;
use Illuminate\Console\Command;
use DrawCrypt;
use URLParser;

class FixOrganizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:organizer {url : The URL received by one of the participants to write to their santa} {email : The correct email of the organizer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix an organizer email';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setCryptKeyFromUrl($this->argument('url'));

        $draw = URLParser::parseByName('dearsanta', $this->argument('url'))->participant->draw;

        $draw->organizer->email = $this->argument('email');
        $draw->organizer->save();

        $draw->organizer->notify(new OrganizerRecap);
        $this->info('Organizer Recap sent');

        $draw->organizer->notify(new TargetDrawn);
        $this->info('Organizer Participant mail sent');
    }

    protected function setCryptKeyFromUrl($url)
    {
        $hash = Arr::get(explode('#', $url, 2), 1);
        $key = base64_decode($hash);
        DrawCrypt::setKey($key);
    }
}
