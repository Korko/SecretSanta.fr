<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use URLParser;

#[Description('Send all participants emails again')]
#[Signature('secretsanta:fix-draw {url : The URL received by one of the participants to write to their santa}')]
final class FixDraw extends Command
{
    use ParsesDrawUrl;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->setCryptIVFromUrl($this->argument('url'));

        $participant = URLParser::parseByName('dearSanta', $this->argument('url'))->participant;
        if ($participant) {
            $draw = $participant->draw;
        } else {
            $draw = URLParser::parseByName('organizerPanel', $this->argument('url'))->draw;
        }

        $draw->participants->each(function ($participant): void {
            $participant->notifyNow(new TargetDrawn);
            $this->info('Participant mail sent');

            $participant->target->dearSantas->each(function ($dearSanta) use ($participant): void {
                $participant->notify(new DearSanta($dearSanta));
                $this->info('DearSanta mail sent');
            });
        });
    }
}
