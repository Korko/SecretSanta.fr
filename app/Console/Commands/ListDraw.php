<?php

namespace App\Console\Commands;

use URLParser;

class ListDraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:list-draw {url : The URL received by one of the participants to write to their santa or the link to the organizer panel}';

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

        $participant = URLParser::parseByName('santa.index', $this->argument('url'))->participant;
        if ($participant) {
            $draw = $participant->draw;
        } else {
            $draw = URLParser::parseByName('organizer.index', $this->argument('url'))->draw;
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Status'],
            $draw->participants()
                ->with(['mail'])
                ->get()
                ->map(fn ($col) => Arr::only($col->toArray(), ['id', 'name', 'email']) + ['delivery_status' => Arr::get($col->toArray(), 'mail.delivery_status')])
                ->toArray()
        );
    }
}
