<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\error;
use function Laravel\Prompts\text;

#[Description('Fix an organizer email and send them again the link to their panel')]
#[Signature('secretsanta:fix-organizer {url? : The URL received by one of the participants to write to their santa} {email? : The correct email of the organizer} {--force : Do not ask for confirmation}')]
final class FixOrganizer extends Command
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

        // Before changing organizer_email, which the legacy organizer detection compares
        $organizer = $draw->organizer;

        // Keeping the current address only sends the emails again
        $email = $this->stringArgument('email') ?? text(
            label: 'What is the correct email of the organizer?',
            default: $draw->organizer_email,
            required: true,
            validate: fn (string $value): ?string => filter_var($value, FILTER_VALIDATE_EMAIL) === false ? 'This is not a valid email' : null,
        );

        $changed = $email !== $draw->organizer_email;
        $question = $changed
            ? 'Change the organizer email from '.$draw->organizer_email.' to '.$email.' and send their emails again?'
            : 'Send the organizer their emails again, to '.$email.'?';
        if (! $this->confirmOrForce($question)) {
            return;
        }

        if ($changed) {
            $draw->organizer_email = $email;
            $draw->save();
        }

        // Like at the draw: taking part, the organizer gets the recap within their target email only
        if ($organizer === null) {
            $draw->recapMail()->markAsCreated();
            $draw->notify(new OrganizerRecap($draw));
            $this->info('Organizer Recap sent');

            return;
        }

        if ($changed) {
            $organizer->email = $email;
            $organizer->save();
        }

        $organizer->notifyNow(new TargetDrawn);
        $this->info('Participant mail sent, with the organizer recap and their target messages');
    }
}
