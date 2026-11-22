<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ParticipantsEditor;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\error;

#[Description('Withdraw a participant and inform their santa')]
#[Signature('secretsanta:withdraw-participant {url : The URL received by one of the participants to write to their santa} {id : The participant id} {--force : Do not ask for confirmation}')]
final class WithdrawParticipant extends Command
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

        if (! $this->confirmOrForce('Withdraw '.$participant->name.' (#'.$participant->id.') and email their santa their new target?')) {
            return;
        }

        // As from the organizer panel: exclusions respected, santa told, their new target's messages forwarded
        try {
            new ParticipantsEditor($draw)->apply([$participant], []);
        } catch (ValidationException $validationException) {
            error($validationException->getMessage());

            return;
        }

        $this->info('Participant withdrawn, their santa informed');
    }
}
