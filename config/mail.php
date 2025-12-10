<?php

return [

    'mailers' => [
        'mailtrap' => [
            'transport' => 'smtp',
            'host' => 'smtp.mailtrap.io',
            'port' => 465,
            'encryption' => 'tls',
            'username' => env('MAILTRAP_USERNAME'),
            'password' => env('MAILTRAP_PASSWORD'),
        ],

        'mailhog' => [
            'transport' => 'smtp',
            'host' => env('MAILHOG_HOST', '127.0.0.1'),
            'port' => env('MAILHOG_PORT', 1025),
            'encryption' => null,
            'username' => null,
            'password' => null,
        ],

        'mailgun' => [
            'transport' => 'mailgun',
        ],

        'brevo' => [
            'transport' => 'smtp',
            'host' => 'smtp-relay.brevo.com',
            'port' => 587,
            'username' => env('BREVO_USERNAME'),
            'password' => env('BREVO_PASSWORD'),
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'sendmail',
            ],
        ],
    ],

    'return_path' => env('MAIL_RETURN_PATH'),

    'username' => env('MAIL_USERNAME'),

    'password' => env('MAIL_PASSWORD'),

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

    'dkim_selector' => env('MAIL_DKIM_SELECTOR'), // selector, required,

    'dkim_domain' => env('MAIL_DKIM_DOMAIN'), // domain, required,

    'dkim_private_key' => env('MAIL_DKIM_PRIVATE_KEY'), // path to private key, required,

    'dkim_identity' => env('MAIL_DKIM_IDENTITY'), // identity (optional),

    'dkim_algo' => env('MAIL_DKIM_ALGO', 'rsa-sha256'), // sign algorithm (defaults to rsa-sha256),

    'dkim_passphrase' => env('MAIL_DKIM_PASSPHRASE'), // private key passphrase (optional),

    'resend_delay' => 5 * 60, // 5m delay,

];
