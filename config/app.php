<?php

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Facade;

return [

    'email' => env('APP_EMAIL', 'webmaster@localhost'),

    'timezone' => 'Europe/Paris',

    'domains' => [
        'fr' => 'secretsanta.fr',
        'en' => 'secretsanta.io',
    ],

    'challenge' => 'Ping?',


    'aliases' => Facade::defaultAliases()->merge([
        'Csv' => App\Services\CsvGenerator::class,
        'DrawCrypt' => App\Facades\DrawCrypt::class,
        'Hashids' => Vinkla\Hashids\Facades\Hashids::class,
        'Solver' => App\Facades\Solver::class,
        'URLParser' => Facades\App\Services\URLParser::class,
    ])->toArray(),

    'bmc' => env('BMC_ME'),

];
