<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

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
        $draw = $this->findOrAskDrawFromUrl($this->stringArgument('url'));

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
