<?php

namespace App\Console\Commands;

use App\Models\Draw;
use Illuminate\Support\Arr;
use function Laravel\Prompts\text;

trait ParsesDrawUrl
{
    protected function setCryptIVFromUrl($url)
    {
        $hash = Arr::get(explode('#', $url, 2), 1);
        $key = base64_decode($hash);
        DrawCrypt::setIV($key);
    }

    protected function findOrAskDrawFromUrl(?string $url): Draw
    {
        $url = $url ?: text(
            label: 'What is the URL?',
            hint: 'This URL was received by one of the participants to write to their santa or is the link to the organizer panel',
            placeholder: config('app.url'),
            required: true
        );

        $this->setCryptIVFromUrl($url);

        $participant = URLParser::parseByName('dearSanta', $url)->participant;
        if ($participant) {
            $draw = $participant->draw;
        } else {
            $draw = URLParser::parseByName('organizerPanel', $url)->draw;
        }

        return $draw;
    }
}
