<?php

declare(strict_types=1);

return [
    'reminder' => 'Denken Sie daran, dass Ihr geheimer Weihnachtsmann oder Ihre geheime Weihnachtsfrau Ihnen nicht antworten kann! Geben sie ihm oder ihr so viele Informationen wie möglich zu geben.',
    'report' => [
        'question' => 'Ein Problem?',
        'not_me' => 'Diese E-Mail ist nicht für mich',
        'not_participating' => 'Ich nehme nicht teil',
        'cancel' => 'Meldung zurückziehen',
        'sent' => [
            'not_me' => '{organizer} sieht auf der eigenen Seite, dass diese E-Mail nicht für Sie war.',
            'not_participating' => '{organizer} sieht auf der eigenen Seite, dass Sie nicht teilnehmen.',
        ],
    ],
    'targets' => [
        'revealed' => 'Der Organisator hat die Liste der Empfänger eingesehen',
        'secret' => 'Der Organisator hat die Liste der Empfänger nicht eingesehen',
        'ended' => 'Die Veranstaltung ist vorbei: die Liste der Empfänger ist jetzt für den Organisator sichtbar',
    ],
    'list' => [
        'date' => 'Versanddatum',
        'body' => 'Nachrichtentext',
        'status' => 'Empfangsstatus',
        'empty' => 'Es wurden noch keine E-Mails gesendet',
        'caption' => 'Liste der E-Mails, die an den Weihnachtsmann/die Weihnachtsfrau gesendet wurden',
    ],
    'content' => [
        'label' => 'E-Mail-Inhalt',
        'placeholder' => 'Lieber Weihnachtsmensch...',
    ],
    'resend' => [
        'button' => 'Sende mir die E-Mails, die ich von meinem Empfänger erhalten habe, erneut.',
    ],
    'email' => [
        'current' => 'Ihre E-Mails gehen an :email.',
        'open' => 'Adresse ändern',
        'new' => 'Neue Adresse',
        'send_code' => 'Code erhalten',
        'code' => 'An :email gesendeter Code:',
        'confirm' => 'Bestätigen',
        'code_sent' => 'Ein Code wurde gerade an :email gesendet. Geben Sie ihn hier ein, um die Änderung zu bestätigen.',
        'changed' => 'Ihre E-Mails gehen ab jetzt an :email. Der Name Ihres Empfängers wurde gerade dorthin erneut gesendet.',
        'wrong_code' => 'Dieser Code ist nicht richtig.',
        'expired' => 'Dieser Code ist nicht mehr gültig, fordern Sie einen neuen an.',
        'same' => 'Das ist bereits Ihre Adresse.',
    ],
];
