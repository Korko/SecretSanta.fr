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

    // A participant's withdrawal takes effect after this many hours (at the latest the morning of the event): the
    // organizer can still fix their address meanwhile, when someone else got their email and withdrew in their place
    'withdraw_delay' => (int) env('WITHDRAW_DELAY', 48),

];
