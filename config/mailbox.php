<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailbox
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailbox that is used to read email
    | messages received by your application. Alternative mailboxes may be setup
    | and used as needed; however, this mailbox will be used by default.
    |
    */

    'default' => env('MAIL_MAILBOX', 'imap'),

    /*
    |--------------------------------------------------------------------------
    | Mailbox Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailboxes used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers to be used while
    | sending an e-mail. You will specify which one you are using for your
    | mailboxes below. You are free to add additional mailboxes as required.
    |
    | Supported: "imap", "array"
    |
    */

    'mailboxes' => [
        'imap' => [
            'account' => 'default',
            'transport' => 'imap'
        ],

        'array' => [
            'transport' => 'array',
        ],
    ],

];
