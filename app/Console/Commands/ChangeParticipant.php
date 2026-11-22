<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\error;

#[Description('Change a participant name and email, send them their target and inform their santa')]
#[Signature('secretsanta:change-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id} {name : The new name of the participant} {email : The new email of the participant} {--force : Do not ask for confirmation}')]
final class ChangeParticipant extends Command
{
    use ConfirmsChanges;
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
        $name = (string) $this->stringArgument('name');
        $email = (string) $this->stringArgument('email');

        // Someone else takes their place: what they wrote to their santa goes
        if (! $this->confirmOrForce('Replace '.$participant->name.' <'.$participant->email.'> (#'.$participant->id.') by '.$name.' <'.$email.'>, delete the messages they wrote to their santa and email both?')) {
            return;
        }

        $participant->dearSantas()->lazy()->each->delete();

        $participant->name = $name;
        $participant->email = $email;
        $participant->save();

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent');

        $participant->forwardTargetMessages();

        $participant->santa->notify(new TargetWithdrawn);
        $this->info('Santa informed');
    }
}
