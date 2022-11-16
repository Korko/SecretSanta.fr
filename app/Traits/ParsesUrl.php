<?php

namespace App\Traits;

use App\Models\Draw;
use DrawCrypt;
use Illuminate\Support\Arr;
use URLParser;

trait ParsesUrl
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

    protected function setCryptIVFromUrl($url)
    {
        $hash = Arr::get(explode('#', $url, 2), 1);
        $key = base64_decode($hash);
        DrawCrypt::setIV($key);
    }
}
