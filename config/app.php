<?php

use App\Facades\DrawCrypt;
use App\Facades\Solver;
use App\Services\CsvGenerator;
use Facades\App\Services\URLParser;
use Illuminate\Support\Facades\Facade;
use Vinkla\Hashids\Facades\Hashids;

return [

    'email' => env('APP_EMAIL', 'webmaster@localhost'),

    'timezone' => 'Europe/Paris',

    'domains' => [
        'fr' => 'secretsanta.fr',
        'en' => 'secretsanta.io',
    ],

    'challenge' => 'Ping?',

    'aliases' => Facade::defaultAliases()->merge([
        'Csv' => CsvGenerator::class,
        'DrawCrypt' => DrawCrypt::class,
        'Hashids' => Hashids::class,
        'Solver' => Solver::class,
        'URLParser' => URLParser::class,
    ])->toArray(),

    'bmc' => env('BMC_ME'),

];
