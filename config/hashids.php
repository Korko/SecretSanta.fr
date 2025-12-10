<?php

/*
 * This file is part of Laravel Hashids.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'santa',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [
        'santa' => [
            'salt' => env('HASHIDS_SALT_SANTA', 'secretsanta-santa'),
            'length' => '5',
        ],

        'draw' => [
            'salt' => env('HASHIDS_SALT_DRAW', 'secretsanta-draw'),
            'length' => '5',
        ],

        'dearSanta' => [
            'salt' => env('HASHIDS_SALT_DEARSANTA', 'secretsanta-dearSanta'),
            'length' => '5',
        ],

        'bounce' => [
            'salt' => env('HASHIDS_SALT_BOUNCE', 'secretsanta-bounce'),
            'length' => '10',
        ],
    ],

];
