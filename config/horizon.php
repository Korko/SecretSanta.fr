<?php

return [
    'use' => 'default',
    'prefix' => env('HORIZON_PREFIX', 'horizon:'),
    'domain' => env('HORIZON_DOMAIN', null),
    'path' => env('HORIZON_PATH', 'horizon'),
    'middleware' => ['web', 'auth', 'admin'],

    'waits' => [
        'redis:default' => 60,
        'redis:draws' => 300,
        'redis:notifications' => 60,
    ],

    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 10080,
    ],

    'metrics' => [
        'trim_snapshots' => [
            'job' => 24,
            'queue' => 24,
        ],
    ],

    'fast_termination' => false,

    'memory_limit' => 128,

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['alerts', 'default'],
                'balance' => 'auto',
                'maxProcesses' => 5,
                'memory' => 128,
                'tries' => 3,
                'timeout' => 60,
                'nice' => 0,
            ],

            'draws-worker' => [
                'connection' => 'redis',
                'queue' => ['draws', 'draws-medium', 'draws-large'],
                'balance' => 'auto',
                'maxProcesses' => 3,
                'memory' => 256,
                'tries' => 3,
                'timeout' => 300,
                'nice' => 0,
            ],

            'notifications-worker' => [
                'connection' => 'redis',
                'queue' => ['notifications', 'emails'],
                'balance' => 'auto',
                'maxProcesses' => 10,
                'memory' => 128,
                'tries' => 5,
                'timeout' => 60,
                'nice' => 0,
            ],
        ],

        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['alerts', 'draws', 'notifications', 'emails', 'default'],
                'balance' => 'simple',
                'maxProcesses' => 3,
                'memory' => 128,
                'tries' => 1,
                'timeout' => 60,
                'nice' => 0,
            ],
        ],
    ],
];
