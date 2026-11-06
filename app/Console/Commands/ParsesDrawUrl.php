<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Draw;
use DrawCrypt;
use Illuminate\Support\Arr;
use URLParser;

use function Laravel\Prompts\text;

trait ParsesDrawUrl
{
    protected function setCryptIVFromUrl($url)
    {
        $hash = Arr::get(explode('#', (string) $url, 2), 1, '');
        $key = base64_decode($hash);
        DrawCrypt::setIV($key);
    }

    protected function findOrAskDrawFromUrl(?string $url): Draw
    {
        $url = $url ?: text(
            label: 'What is the URL?',
            placeholder: config('app.url'),
            required: true,
            hint: 'This URL was received by one of the participants to write to their santa or is the link to the organizer panel'
        );

        $this->setCryptIVFromUrl($url);

        $participant = URLParser::parseByName('dearSanta', $url)->participant;
        if ($participant) {
            return $participant->draw;
        }

        return URLParser::parseByName('organizerPanel', $url)->draw;
    }
}
