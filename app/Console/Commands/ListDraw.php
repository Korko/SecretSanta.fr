<?php

namespace App\Console\Commands;

use App\Traits\Console\ListsDraw;
use App\Traits\ParsesUrl;
use Illuminate\Console\Command;

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
     *
     * @return mixed
     */
    public function handle()
    {
        $draw = $this->getDrawFromURL($this->argument('url'));
        $this->displayDraw($draw);
    }
}
