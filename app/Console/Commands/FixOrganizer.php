<?php

namespace App\Console\Commands;

use App\Draw;
use Arr;
use Crypt;
use DrawHandler;
use Hashids;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $this->setCryptKeyFromUrl($this->argument('url'));

        $draw = Draw::getDrawFromDearSantaUrl($this->argument('url'));

        $draw->organizer->email = $this->argument('email');
        $draw->organizer->save();

        DrawHandler::contactOrganizer($draw, false);
        $this->info('Organizer Recap sent');

        DrawHandler::contactParticipant($draw->organizer);
        $this->info('Organizer Participant mail sent');
    }

    protected function setCryptKeyFromUrl($url)
    {
        $hash = Arr::get(explode('#', $url, 2), 1);
        $key = base64_decode($hash);
        Crypt::setKey($key);
    }
}
