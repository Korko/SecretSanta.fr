<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers to be used while
    | sending an e-mail. You will specify which one you are using for your
    | mailers below. You are free to add additional mailers as required.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses",
    |            "postmark", "log", "array", "failover"
    |
    */

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

        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
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

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    'return_path' => env('MAIL_RETURN_PATH'),

    'username' => env('MAIL_USERNAME'),

    'password' => env('MAIL_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

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
