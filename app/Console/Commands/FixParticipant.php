<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\TargetDrawn;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\error;

#[Description('Fix a participant email and send them again their target')]
#[Signature('secretsanta:fix-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id} {email? : The correct email of the participant} {--force : Do not ask for confirmation}')]
final class FixParticipant extends Command
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

        $email = $this->stringArgument('email');

        $question = $email !== null
            ? 'Change the email of '.$participant->name.' (#'.$participant->id.') from '.$participant->email.' to '.$email.' and send them their target again?'
            : 'Send '.$participant->name.' (#'.$participant->id.') their target again, to '.$participant->email.'?';
        if (! $this->confirmOrForce($question)) {
            return;
        }

        if ($email !== null) {
            $participant->email = $email;
            $participant->save();
        }

        $participant->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent, with their target messages');
    }
}
