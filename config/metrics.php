<?php

declare(strict_types=1);

// InfluxDB 1.8+ through its v2-compatible API (influxdb-php, the v1 client, is abandoned).
// Same IDB_* variables as before: the v1 credentials become a "user:password" token.
return [
    'default' => env('METRICS_BACKEND'),
    'backends' => [
        'influxdb' => [
            'version' => 2,
            'host' => 'http://'.env('IDB_HOST', 'localhost'),
            'tcp_port' => env('IDB_TCP_PORT', 8086),
            'database' => env('IDB_DATABASE'),
            'token' => env('IDB_USERNAME', '').':'.env('IDB_PASSWORD', ''),
            'org' => '-',
        ],
    ],
];
