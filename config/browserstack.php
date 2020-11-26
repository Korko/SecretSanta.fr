<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | Your username and access key for BrowserStack.
    | https://www.browserstack.com/accounts/settings
    |
    */

    'username' => env('BROWSERSTACK_USERNAME'),

    'key' => env('BROWSERSTACK_ACCESS_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Browser
    |--------------------------------------------------------------------------
    |
    | The browser slug to run on BrowserStack.
    |
    */

    'browser' => env('BROWSERSTACK_BROWSER', 'WINDOWS_10_CHROME'),

    /*
    |--------------------------------------------------------------------------
    | Session
    |--------------------------------------------------------------------------
    |
    | Configuration to make BrowserStack run each tests in a different
    | session.
    |
    */

    'separate_sessions' => env('BROWSERSTACK_SEPARATE_SESSIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Capabilities
    |--------------------------------------------------------------------------
    |
    | The configuration for capabilities of the browser.
    | https://www.browserstack.com/automate/capabilities
    |
    */

    'capabilities' => [

        'acceptSslCerts' => env('BROWSERSTACK_ACCEPT_SSL', true),

        'browserstack' => [

            'console' => env('BROWSERSTACK_CONSOLE', 'verbose'),

            'local' => env('BROWSERSTACK_LOCAL_TUNNEL', true),

            'timezone' => env('BROWSERSTACK_TIMEZONE', config('app.timezone')),

        ],

        'resolution' => env('BROWSERSTACK_RESOLUTION', '1920x1080'),

    ],

    /*
    |--------------------------------------------------------------------------
    | Command Arguments
    |--------------------------------------------------------------------------
    |
    | The arguments and flags to use for the BrowserStack local connection.
    |
    */

    'arguments' => [

        'binaryPath' => env('BROWSERSTACK_CLI_BINARY_PATH'),

        'logFile' => env('BROWSERSTACK_CLI_LOG_FILE'),

        'v' => env('BROWSERSTACK_CLI_VERBOSE'),

        'force' => env('BROWSERSTACK_CLI_FORCE'),

        'only' => env('BROWSERSTACK_CLI_ONLY'),

        'onlyAutomate' => env('BROWSERSTACK_CLI_ONLY_AUTOMATE'),

        'forcelocal' => env(
            'BROWSERSTACK_CLI_FORCE_LOCAL',
            env('BROWSERSTACK_LOCAL_TUNNEL', true)
        ),

        'localIdentifier' => env('BROWSERSTACK_CLI_LOCAL_IDENTIFIER'),

        'proxyHost' => env('BROWSERSTACK_CLI_PROXY_HOST'),

        'proxyPort' => env('BROWSERSTACK_CLI_PROXY_PORT'),

        'proxyUser' => env('BROWSERSTACK_CLI_PROXY_USER'),

        'proxyPass' => env('BROWSERSTACK_CLI_PROXY_PASSWORD'),

        'forceproxy' => env('BROWSERSTACK_CLI_FORCE_PROXY'),

        'hosts' => env('BROWSERSTACK_CLI_HOSTS'),

        'f' => env('BROWSERSTACK_CLI_F'),

    ],
];
