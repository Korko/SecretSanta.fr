<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ParticipantLimit;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

#[Description('List the links for bigger draws (secretsanta:allow-participants), active and used')]
#[Signature('secretsanta:participant-limits')]
final class ListParticipantLimits extends Command
{
    public function handle(): int
    {
        $limits = ParticipantLimit::query()->orderByDesc('id')->get();
        if ($limits->isEmpty()) {
            info('No link');

            return self::SUCCESS;
        }

        // Only the hash of the codes is stored: the links themselves cannot be shown again
        table(
            ['ID', 'Max', 'Created', 'Status', 'Used', 'Draw'],
            $limits->map(fn (ParticipantLimit $limit): array => [
                (string) $limit->id,
                (string) $limit->max,
                $limit->created_at?->toDateTimeString() ?? '',
                $limit->used_at !== null ? 'used' : 'active',
                $limit->used_at?->toDateTimeString() ?? '',
                $limit->draw_id !== null ? (string) $limit->draw_id : '',
            ])->all(),
        );

        return self::SUCCESS;
    }
}
