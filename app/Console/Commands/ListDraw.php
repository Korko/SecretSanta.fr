<?php

namespace App\Console\Commands;

use Arr;
use Illuminate\Console\Command;
use DrawCrypt;
use URLParser;

class ListDraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:draw {url : The URL received by one of the participants to write to their santa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List draw participants';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setCryptIVFromUrl($this->argument('url'));

        $participant = URLParser::parseByName('dearSanta', $this->argument('url'))->participant;
        if($participant) {
            $draw = $participant->draw;
        } else {
            $draw = URLParser::parseByName('organizerPanel', $this->argument('url'))->draw;
        }

        $this->table(
            ['ID', 'Name', 'Email'],
            $draw->participants()->get(['id', 'name', 'email'])->toArray()
        );
    }

    protected function setCryptIVFromUrl($url)
    {
        $hash = Arr::get(explode('#', $url, 2), 1);
        $key = base64_decode($hash);
        DrawCrypt::setIV($key);
    }
}
