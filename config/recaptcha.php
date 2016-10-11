<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site key and Secret
    |--------------------------------------------------------------------------
    |
    | These two values are required to authenticate yourself to Google's
    | Recaptcha service.
    |
    */

    'sitekey' => env('RECAPTCHA_SITEKEY', ''),
    'secret' => env('RECAPTCHA_SECRET', '')
];