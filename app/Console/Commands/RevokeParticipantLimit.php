<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ParticipantLimit;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

#[Description('Delete a link for a bigger draw, by its ID (secretsanta:participant-limits)')]
#[Signature('secretsanta:revoke-participant-limit {id : The ID shown by secretsanta:participant-limits}')]
final class RevokeParticipantLimit extends Command
{
    public function handle(): int
    {
        $limit = ParticipantLimit::query()->find($this->argument('id'));
        if (! $limit instanceof ParticipantLimit) {
            error('No link with this ID');

            return self::FAILURE;
        }

        $limit->delete();

        if ($limit->used_at !== null) {
            warning('Already used: the draw keeps its limit of '.$limit->max.' participants');
        }

        info('Link #'.$limit->id.' deleted');

        return self::SUCCESS;
    }
}
