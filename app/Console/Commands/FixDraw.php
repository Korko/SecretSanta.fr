<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\TargetDrawn;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Description('Send all participants emails again')]
#[Signature('secretsanta:fix-draw {url : The URL received by one of the participants to write to their santa} {--force : Do not ask for confirmation}')]
final class FixDraw extends Command
{
    use ConfirmsChanges;
    use ParsesDrawUrl;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $draw = $this->findOrAskDrawFromUrl($this->stringArgument('url'));

        if (! $this->confirmOrForce('Send their target again to the '.$draw->participants->count().' participants of draw #'.$draw->id.'?')) {
            return;
        }

        $draw->participants->each(function ($participant): void {
            $participant->notifyNow(new TargetDrawn);
            $this->info('Participant mail sent, with their target messages');
        });
    }
}
