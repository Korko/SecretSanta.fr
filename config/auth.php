<?php

declare(strict_types=1);

return [

    'guards' => [
        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

];
