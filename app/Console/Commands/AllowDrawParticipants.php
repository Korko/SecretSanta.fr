<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

// For a draw already made: the organizer can then add participants up to this limit from their panel
#[Description('Change the participants limit of an existing draw (like secretsanta:allow-participants for a new one)')]
#[Signature('secretsanta:allow-draw-participants {url : The URL received by one of the participants or the link to the organizer panel} {max : The new participants limit for this draw}')]
final class AllowDrawParticipants extends Command
{
    use ParsesDrawUrl;

    public function handle(): int
    {
        try {
            $draw = $this->findOrAskDrawFromUrl($this->stringArgument('url'));
        } catch (Exception $exception) {
            error('Draw not found - '.$exception->getMessage());

            return self::FAILURE;
        }

        $max = filter_var($this->argument('max'), FILTER_VALIDATE_INT);
        $count = $draw->participants()->count();
        if (! is_int($max) || $max < max(3, $count)) {
            error('The limit must be an integer, at least the current number of participants ('.$count.')');

            return self::FAILURE;
        }

        $draw->max_participants = $max;
        $draw->save();

        info('Draw #'.$draw->id.': up to '.$max.' participants ('.$count.' now)');

        return self::SUCCESS;
    }
}
