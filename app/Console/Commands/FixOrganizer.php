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

        $participantOrganizer = false;
        if ($draw->organizer->email === $draw->organizer_email) {
            $participantOrganizer = true;
        }

        $email = $this->stringArgument('email');
        if ($email !== null) {
            $draw->organizer_email = $email;
            $draw->save();
        }

        $draw->recapMail()->markAsCreated();
        $draw->notify(new OrganizerRecap($draw));
        $this->info('Organizer Recap sent');

        if ($participantOrganizer) {
            if ($email !== null) {
                $draw->organizer->email = $email;
                $draw->organizer->save();
            }

            $draw->organizer->notifyNow(new TargetDrawn);
            $this->info('Participant mail sent');

            $draw->organizer->target->dearSantas->each(function ($dearSanta) use ($draw): void {
                $draw->organizer->notify(new DearSanta($dearSanta));
                $this->info('DearSanta mail sent');
            });
        }
    }
}
