<?php

namespace App\Console\Commands;

use DrawCrypt;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Support\Arr;

abstract class Command extends BaseCommand
{
    protected function setCryptIVFromUrl($url)
    {
        $hash = Arr::get(explode('#', $url, 2), 1);
        $key = base64_decode($hash);
        DrawCrypt::setIV($key);
    }
}
