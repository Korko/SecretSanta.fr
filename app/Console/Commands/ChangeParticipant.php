<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\error;

#[Description('Change a participant name and email, send them their target and inform their santa')]
#[Signature('secretsanta:change-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id} {name : The new name of the participant} {email : The new email of the participant}')]
final class ChangeParticipant extends Command
{
    use ParsesDrawUrl;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $draw = $this->findOrAskDrawFromUrl($this->argument('url'));
        } catch (Exception $exception) {
            error('Draw not found - '.$exception->getMessage());
            report($exception);

            return;
        }

        $participant = $draw->participants->find($this->argument('id'));

        $participant->dearSantas()->lazy()->each->delete();

        $participant->name = $this->argument('name');
        $participant->email = $this->argument('email');
        $participant->save();

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');

        $participant->target->dearSantas->each(function ($dearSanta) use ($participant): void {
            $participant->notify(new DearSanta($dearSanta));
        });

        $participant->santa->notify(new TargetWithdrawn);
        $this->info('Santa informed');
    }
}
