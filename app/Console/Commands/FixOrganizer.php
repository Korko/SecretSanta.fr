<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\DearSanta;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\error;
use function Laravel\Prompts\text;

#[Description('Fix an organizer email and send them again the link to their panel')]
#[Signature('secretsanta:fix-organizer {url? : The URL received by one of the participants to write to their santa} {email? : The correct email of the organizer}')]
final class FixOrganizer extends Command
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
        if ($changed) {
            $draw->organizer_email = $email;
            $draw->save();
        }

        $draw->recapMail()->markAsCreated();
        $draw->notify(new OrganizerRecap($draw));
        $this->info('Organizer Recap sent');

        if ($organizer !== null) {
            if ($changed) {
                $organizer->email = $email;
                $organizer->save();
            }

            $organizer->notifyNow(new TargetDrawn);
            $this->info('Participant mail sent');

            $organizer->target->dearSantas->each(function ($dearSanta) use ($organizer): void {
                $organizer->notify(new DearSanta($dearSanta));
                $this->info('DearSanta mail sent');
            });
        }
    }
}
