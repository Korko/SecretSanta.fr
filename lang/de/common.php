<?php

declare(strict_types=1);

return [
    'internal' => 'Ein Fehler ist aufgetreten',
    'success' => 'Erfolg',

    'copy_failed' => 'Kopieren nicht möglich: Kopieren Sie :text von Hand',
    'copied' => 'In die Zwischenablage kopiert',

    'fetcher' => [
        'load' => 'Laden',
        'loading' => 'Ladevorgang läuft...',
    ],

    'form' => [
        'send' => 'Senden',
        'sending' => 'Wird versendet...',
        'sent' => 'Gesendet',
        'reset' => 'Eine neue Ziehung beginnen',
    ],

    'modal' => [
        'close' => 'Schließen',
    ],

    'email' => [
        'redo' => 'Erneut senden',
        'status' => [
            'created' => 'Warten auf die Versendung',
            'sending' => 'Senden in Bearbeitung',
            'sent' => 'Gesendet',
            'error' => 'Fehler',
            'delayed' => 'Verzögert, neuer Versuch läuft',
            'received' => 'Empfangen',
        ],
        'recent' => 'Sie können dieselbe E-Mail nicht zu schnell erneut versenden',
        // Why it bounced, when the delivery report says (BounceReason)
        'bounce' => [
            'address' => 'Diese Adresse existiert nicht: bitte korrigieren',
            'full' => 'Postfach voll: später erneut senden',
            'refused' => 'Vom Server des Empfängers abgelehnt, die Adresse ist nicht das Problem',
        ],
    ],

    'csv' => [
        'generated' => 'Datei erstellt am :date auf :app (:url)',
        'usage' => 'Mit dieser Datei können die Teilnehmer und ihre Ausschlüsse vorausgefüllt werden',
    ],
];
