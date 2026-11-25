<?php

declare(strict_types=1);

return [
    'reminder' => 'Denken Sie daran, dass Ihr geheimer Weihnachtsmann oder Ihre geheime Weihnachtsfrau Ihnen nicht antworten kann! Geben sie ihm oder ihr so viele Informationen wie möglich zu geben.',
    'report' => [
        'question' => 'Ein Problem?',
        'identity' => 'Sind Sie {name}?',
        'not_me' => 'Nein, diese E-Mail ist nicht für mich',
        'its_me' => 'Ja, das bin ich',
        'not_participating' => 'Ich wollte nie teilnehmen',
        'cancel' => 'Meldung zurückziehen',
        'back' => 'Zurück',
        'sent' => [
            'not_me' => '{organizer} wurde per E-Mail benachrichtigt, dass diese E-Mail nicht für Sie war, um die Adresse zu korrigieren.',
            'not_participating' => '{organizer} wurde per E-Mail benachrichtigt, dass Sie nicht teilnehmen.',
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
    'withdraw' => [
        'button' => 'Ich möchte nicht mehr teilnehmen',
        'done' => 'Sie nehmen nicht mehr an dieser Veranstaltung teil. Ihr geheimer Weihnachtsmann hat einen neuen Empfänger erhalten und die Organisation wurde benachrichtigt.',
        'confirm' => [
            'title' => 'Von der Veranstaltung abmelden?',
            'body' => 'Sie verlassen die Auslosung: Ihr geheimer Weihnachtsmann erhält einen neuen Empfänger, {organizer} wird per E-Mail benachrichtigt und diese Seite funktioniert nicht mehr. Nur {organizer} kann Sie wieder aufnehmen. Wenn diese E-Mail nicht für Sie bestimmt war oder Sie nie teilnehmen wollten, nutzen Sie stattdessen die Meldung.',
            'help' => 'Geben Sie unten Ihren Namen „{name}“ ein, um zu bestätigen.',
            'ok' => 'Abmelden',
            'cancel' => 'Abbrechen',
        ],
        'blocked' => 'Ihre Abmeldung würde jemandem eine ausgeschlossene Person zuteilen. Bitten Sie :organizer, Sie über die Organisationsseite zu entfernen.',
    ],
    'suggestion' => [
        'open' => 'Eine Geschenkidee für jemand anderen vorschlagen',
        'help' => 'Ihre Idee wird per E-Mail an den geheimen Weihnachtsmann der gewählten Person gesendet, ohne dass Sie erfahren, wer das ist. Sie bleibt anonym: Unterschreiben Sie sie im Text, wenn Sie möchten.',
        'recipient' => 'Für wen?',
        'choose' => 'Einen Teilnehmer wählen',
        'content' => 'Ihre Idee',
        'placeholder' => 'Sie liebt Brettspiele...',
        'send' => 'Idee senden',
        'list' => 'Gesendete Ideen',
        'received' => [
            'title' => 'Erhaltene Geschenkideen für Ihren Empfänger',
        ],
    ],
];
