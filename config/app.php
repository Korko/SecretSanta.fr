<?php

declare(strict_types=1);

use App\Facades\DrawCrypt;
use App\Facades\Solver;
use Illuminate\Support\Facades\Facade;
use Vinkla\Hashids\Facades\Hashids;

return [

    'email' => env('APP_EMAIL', 'webmaster@localhost'),

    'timezone' => 'Europe/Paris',

    'locale' => env('APP_LOCALE', 'fr'),

    'domains' => [
        'fr' => env('APP_DOMAIN_FR'),
        'en' => env('APP_DOMAIN_EN'),
    ],

    'challenge' => 'Ping?',

    'aliases' => Facade::defaultAliases()->merge([
        'DrawCrypt' => DrawCrypt::class,
        'Hashids' => Hashids::class,
        'Solver' => Solver::class,
    ])->toArray(),

    'bmc' => env('BMC_ME'),

    // Bigger draws (and more emails sent at once) go through the administrator: keeps the site from being a spam relay
    'max_participants' => (int) env('MAX_PARTICIPANTS', 50),
    'contact' => env('CONTACT_EMAIL', 'webmaster@secretsanta.fr'),

];
