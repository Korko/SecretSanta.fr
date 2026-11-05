<?php

namespace App\Console\Commands;

use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use URLParser;

class FixDraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:fix-draw {url : The URL received by one of the participants to write to their santa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all participants emails again';

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

        $draw->participants->each(function ($participant) {
            $participant->notifyNow(new TargetDrawn);
            $this->info('Participant mail sent');

            $participant->target->dearSantas->each(function ($dearSanta) use ($participant) {
                $participant->notify(new DearSanta($dearSanta));
                $this->info('DearSanta mail sent');
            });
        });
    }
}
