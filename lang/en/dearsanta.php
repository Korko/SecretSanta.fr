<?php

declare(strict_types=1);

return [
    'reminder' => 'Remember that your Secret Santa cannot reply to you! Make sure to give them as much information as possible.',
    'report' => [
        'question' => 'A problem?',
        'identity' => 'Are you {name}?',
        'not_me' => 'No, this email isn\'t for me',
        'its_me' => 'Yes, it\'s me',
        'not_participating' => 'I never asked to take part',
        'cancel' => 'Cancel the report',
        'back' => 'Back',
        'sent' => [
            'not_me' => '{organizer} was told by email that this email wasn\'t for you, to fix the address.',
            'not_participating' => '{organizer} was told by email that you are not taking part.',
        ],
    ],
    'targets' => [
        'revealed' => 'The organizer has viewed the list of targets',
        'secret' => 'The list of targets has not been viewed by the organizer',
        'ended' => 'The event is over: the list of targets is now visible to the organizer',
    ],
    'list' => [
        'date' => 'Sent on',
        'body' => 'Message body',
        'status' => 'Email delivery status',
        'empty' => 'No email sent yet',
        'caption' => 'List of emails sent to your Santa',
    ],
    'content' => [
        'label' => 'Email content',
        'placeholder' => 'Dear Santa...',
    ],
    'resend' => [
        'button' => 'Resend me the emails I received from my target',
    ],
    'email' => [
        'current' => 'Your emails go to :email.',
        'open' => 'Change address',
        'new' => 'New address',
        'send_code' => 'Get a code',
        'code' => 'Code sent to :email:',
        'confirm' => 'Confirm',
        'code_sent' => 'A code has just been sent to :email. Type it here to confirm the change.',
        'changed' => 'Your emails will now go to :email. Your target\'s name has just been sent there again.',
        'wrong_code' => 'This code is not the right one.',
        'expired' => 'This code is no longer valid, ask for a new one.',
        'same' => 'This is already your address.',
    ],
    'withdraw' => [
        'button' => 'I no longer want to take part',
        'done' => 'You no longer take part in this event. Your secret santa got a new target and the organizer has been told.',
        'confirm' => [
            'title' => 'Withdraw from the event?',
            'body' => 'You will leave the draw for good: your secret santa will get a new target, {organizer} will be told by email and this page will stop working. If this email was not meant for you or you never asked to take part, use the report instead.',
            'help' => 'Type your name, "{name}", below to confirm.',
            'ok' => 'Withdraw',
            'cancel' => 'Cancel',
        ],
        'blocked' => 'Your withdrawal would give someone a person they excluded. Ask :organizer to remove you from their organizer page.',
    ],
    'suggestion' => [
        'open' => 'Suggest a gift idea for someone else',
        'help' => 'Your idea will be emailed to the secret santa of the person you choose, without you knowing who they are.',
        'recipient' => 'For whom?',
        'choose' => 'Choose a participant',
        'content' => 'Your idea',
        'placeholder' => 'She loves board games...',
        'signed' => 'Sign with my name (otherwise, the idea stays anonymous)',
        'signed_short' => 'signed',
        'send' => 'Send the idea',
        'list' => 'Ideas sent',
        'received' => [
            'title' => 'Gift ideas received for your target',
            'anonymous' => 'Anonymous',
        ],
    ],
];
