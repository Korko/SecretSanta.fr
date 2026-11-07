<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\error;

#[Description('Fix a participant email and send them again their target')]
#[Signature('secretsanta:fix-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id} {email? : The correct email of the participant}')]
final class FixParticipant extends Command
{
    use ParsesDrawUrl;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $draw = $this->findOrAskDrawFromUrl($this->stringArgument('url'));
        } catch (Exception $exception) {
            error('Draw not found - '.$exception->getMessage());
            report($exception);

            return;
        }

        $participant = $this->findParticipant($draw, $this->argument('id'));

        $email = $this->stringArgument('email');
        if ($email !== null) {
            $participant->email = $email;
            $participant->save();
        }

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');

        $participant->target->dearSantas->each(function ($dearSanta) use ($participant): void {
            $participant->notify(new DearSanta($dearSanta));
            $this->info('DearSanta mail sent');
        });
    }
}
