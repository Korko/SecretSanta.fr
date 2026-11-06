<?php

declare(strict_types=1);

return [
    'reminder' => 'Remember that your Secret Santa cannot reply to you! Make sure to give them as much information as possible.',
    'report' => [
        'question' => 'A problem?',
        'not_me' => 'This email isn\'t for me',
        'not_participating' => 'I\'m not taking part',
        'cancel' => 'Cancel the report',
        'sent' => [
            'not_me' => '{organizer} will see on their page that this email wasn\'t for you.',
            'not_participating' => '{organizer} will see on their page that you are not taking part.',
        ],
    ],
    'targets' => [
        'revealed' => 'The organizer has viewed the list of targets',
        'secret' => 'The list of targets has not been viewed by the organizer',
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
];
