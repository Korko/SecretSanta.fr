<?php

namespace App\Actions;

use App\Models\Draw;
use Illuminate\Database\Eloquent\Collection;

class GenerateDrawCsv
{
    const UTF8_BOM = "\xEF\xBB\xBF";

    protected function generateCsv(Collection $participants): string
    {
        return
            self::UTF8_BOM.
            $participants
                ->toCsv(['name', 'email', 'exclusionsNames'])
                ->prepend([
                    ['# Fichier généré le '.date('d-m-Y').' sur '.config('app.name').' ('.config('app.url').')'],
                    ['# Ce fichier peut être utilisé pour préremplir les participants ainsi que les exclusions associées'],
                ]);
    }

    public function generateInitial(Draw $draw): string
    {
        return $this->generateCsv($draw->participants->loadMissing('exclusions'));
    }

    public function generateFinal(Draw $draw): string
    {
        return $this->generateCsv($draw->participants->loadMissing('exclusions')->appendTargetToExclusions());
    }
}
