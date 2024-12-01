<?php

namespace App\Console\Commands;

use Arr;
use DrawCrypt;
use Illuminate\Console\Command;
use URL;
use URLParser;

class ShowDraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:show-draw {url : The URL received by one of the participants to write to their santa or the link to the organizer panel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show draw';

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

        if(!$draw) {
            $this->error('Draw not found');
            return;
        }

        $this->info('Draw #'.$draw->id.' - '.$draw->mail_title.' ('.$draw->expires_at.')');
        $this->info('Organizer: '.$draw->organizer_name.' <'.$draw->organizer_email.'>');
        $this->comment(URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getIV()));
        $this->newLine();

        $this->table(
            ['ID', 'Name', 'Email', 'Status', 'Target'],
            $draw->participants()
                ->with(['mail', 'target'])
                ->get()
                ->map(fn($col) => Arr::only($col->toArray(), ['id', 'name', 'email']) + ['delivery_status' => Arr::get($col->toArray(), 'mail.delivery_status'), 'target' => $col->target->id.' ('.$col->target->name.')'])
                ->toArray()
        );
        $this->newLine();

        $this->info('Body: '.$draw->mail_body);
    }

    protected function setCryptIVFromUrl($url)
    {
        $hash = Arr::get(explode('#', $url, 2), 1);
        $key = base64_decode($hash);
        DrawCrypt::setIV($key);
    }
}