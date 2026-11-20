<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ParticipantLimit;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

#[Description('Create a single use link to make a draw bigger than MAX_PARTICIPANTS')]
#[Signature('secretsanta:allow-participants {max : The participants limit for the draw made with this link}')]
final class AllowParticipants extends Command
{
    public function handle(): int
    {
        $max = filter_var($this->argument('max'), FILTER_VALIDATE_INT);
        if (! is_int($max) || $max <= config()->integer('app.max_participants')) {
            error('The limit must be an integer above the default one ('.config()->integer('app.max_participants').')');

            return self::FAILURE;
        }

        $code = ParticipantLimit::generate($max);

        info('Link for one draw of up to '.$max.' participants:');
        note(url('/').'?limit='.$code);

        return self::SUCCESS;
    }
}
