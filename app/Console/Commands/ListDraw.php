<?php

namespace App\Console\Commands;

use App\Facades\DrawCrypt;
use App\Traits\Console\ListsDraw;
use App\Traits\ParsesUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;

class ListDraw extends Command
{
    use ParsesUrl, ListsDraw;

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
     */
    public function handle(): void
    {
        $draw = $this->getDrawFromURL($this->argument('url'));

        $this->info('Draw #'.$draw->ulid.' ('.$draw->expires_at.')');
        $this->info('Organizer: '.$draw->organizer_name.' <'.$draw->organizer_email.'>');
        $this->comment(URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getIV()));
        $this->newLine();

        $this->displayDraw($draw);
    }
}
