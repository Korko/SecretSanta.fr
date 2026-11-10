<?php

declare(strict_types=1);

namespace App\Collections;

use App\Models\Participant;
use App\Services\CsvGenerator;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Facades\Date;

/**
 * @extends BaseCollection<int, Participant>
 */
final class ParticipantsCollection extends BaseCollection
{
    public function redrawables(): self
    {
        return $this->filter(fn ($participant) => $participant->redraw);
    }

    public function appendTargetToExclusions(): self
    {
        return (clone $this)->each(function (Participant $participant): void {
            $participant->exclusions->add($participant->target);
        });
    }

    // Reusable to prefill the form next time, with the UTF-8 BOM for Excel
    public function toCsv(): string
    {
        return "\xEF\xBB\xBF".new CsvGenerator([
            ['# '.trans('common.csv.generated', ['date' => Date::now()->isoFormat('L'), 'app' => config()->string('app.name'), 'url' => config()->string('app.url')])],
            ['# '.trans('common.csv.usage')],
            ...$this->map(fn (Participant $participant): array => [
                $participant->name,
                $participant->email,
                implode(',', $participant->exclusionsNames),
            ])->values()->all(),
        ]);
    }
}
