<?php

namespace App\Console\Commands;

use Illuminate\Support\Arr;
use DrawCrypt;
use Illuminate\Console\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    protected function setCryptIVFromUrl($url)
    {
        $hash = Arr::get(explode('#', $url, 2), 1);
        $key = base64_decode($hash);
        DrawCrypt::setIV($key);
    }
}
