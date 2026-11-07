<?php

declare(strict_types=1);

namespace App\Collections;

use App\Models\Participant;
use App\Services\CsvGenerator;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

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
            ['# Fichier généré le '.date('d-m-Y').' sur '.config()->string('app.name').' ('.config()->string('app.url').')'],
            ['# Ce fichier peut être utilisé pour préremplir les participants ainsi que les exclusions associées'],
            ...$this->map(fn (Participant $participant): array => [
                $participant->name,
                $participant->email,
                implode(',', $participant->exclusionsNames),
            ])->values()->all(),
        ]);
    }
}
