<?php

namespace App\Console\Commands;

use App\Models\Draw;
use App\Traits\ParsesUrl;
use Arr;
use Illuminate\Console\Command;

class ListDraw extends Command
{
    use ParsesUrl;

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

    public function displayDraw(Draw $draw): void
    {
        $this->table(
            ['ID', 'Name', 'Email', 'Status'],
            $draw->participants()
                ->with('mail')
                ->get()
                ->map(function ($col) {
                    return
                        Arr::only($col->toArray(), ['id', 'name', 'email']) +
                        ['delivery_status' => Arr::get($col->toArray(), 'mail.delivery_status')];
                })
                ->toArray()
        );
    }
}
