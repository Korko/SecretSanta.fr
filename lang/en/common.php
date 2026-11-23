<?php

declare(strict_types=1);

return [
    'internal' => 'An error occurred',
    'success' => 'Success',

    'copy_failed' => 'Could not copy: copy :text by hand',
    'copied' => 'Copied to clipboard',

    'fetcher' => [
        'load' => 'Load',
        'loading' => 'Loading...',
    ],

    'form' => [
        'send' => 'Send',
        'sending' => 'Sending',
        'sent' => 'Sent',
        'reset' => 'Start a new draw',
    ],

    'modal' => [
        'close' => 'Close',
    ],

    'email' => [
        'redo' => 'Resend',
        'status' => [
            'created' => 'Waiting to be sent',
            'sending' => 'Sending',
            'sent' => 'Sent',
            'error' => 'Error',
            'delayed' => 'Delayed, retrying',
            'received' => 'Received',
        ],
        'recent' => 'You cannot resend the same email so quickly',
        // Why it bounced, when the delivery report says (BounceReason)
        'bounce' => [
            'address' => 'This address doesn\'t exist: fix it',
            'full' => 'Mailbox full: send it again later',
            'refused' => 'Refused by the recipient\'s server, the address isn\'t the problem',
        ],
    ],

    'csv' => [
        'generated' => 'File generated on :date on :app (:url)',
        'usage' => 'This file can be used to prefill the participants and their exclusions',
    ],
];
