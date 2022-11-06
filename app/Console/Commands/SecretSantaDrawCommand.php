<?php

namespace App\Console\Commands;

use App\Models\Draw;
use Illuminate\Support\Arr;
use URLParser;

abstract class SecretSantaDrawCommand extends Command
{
    public function getDrawFromURL(string $url): Draw
    {
        $this->setCryptIVFromUrl($url);

        $participant = URLParser::parseByName('santa.index', $url)->participant;
        if ($participant) {
            $draw = $participant->draw;
        } else {
            $draw = URLParser::parseByName('organizer.index', $url)->draw;
        }

        return $draw;
    }

    public function displayDraw(Draw $draw): void
    {
        $this->table(
            ['ID', 'Name', 'Email', 'Status'],
            $draw->participants()
                ->with('mail')
                ->get()
                ->map(function ($col) {
                    return
                        Arr::only($col->toArray(), ['id', 'name', 'email']) +
                        ['delivery_status' => Arr::get($col->toArray(), 'mail.delivery_status')];
                })
                ->toArray()
        );
    }
}
