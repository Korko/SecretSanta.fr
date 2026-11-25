<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ParticipantsEditor;
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

        new ParticipantsEditor($draw)->replace($participant, $name, $email, now: true);
        $this->info('Participant mail sent, with their target messages, and santa informed');
    }
}
