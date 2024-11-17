<?php

return [
    'url' => 'Link, den ein(e) Teilnehmer(in) per E-Mail erhalten hat',
    'fix' => 'Korrigieren',
    'list' => [
        'name'     => 'Name',
        'email'    => 'E-Mail-Adresse',
        'status'   => 'E-Mail-Versandstatus',
        'caption'  => 'Liste der Teilnehmer/innen',
        'withdraw' => 'Entfernen',
    ],
    'up_and_sent'   => 'Erfolgreich geändert!',
    'withdrawn'     => ':name nimmt nicht mehr an der Veranstaltung teil.',
    'deleted'       => 'Alle Daten wurden gelöscht',
    'download'      => [
        'button'  => 'Zusammenfassung herunterladen',
        'button-tooltip' => [
            'title' => 'Zusammenfassung',
            'content' => 'Dies sind die Daten, wie Sie sie bei der Erstellung der Veranstaltung eingegeben haben. Nur die E-Mail-Adressen können sich geändert haben, um Änderungen widerzuspiegeln, die Sie hier vorgenommen haben.',
        ],
        'button_initial'  => 'Ursprüngliche Zusammenfassung herunterladen',
        'button_initial-tooltip' => [
            'title' => 'Ursprüngliche Zusammenfassung',
            'content' => 'Dies sind die Daten, wie Sie sie bei der Erstellung der Veranstaltung eingegeben haben. Nur die E-Mail-Adressen können sich geändert haben, um Änderungen widerzuspiegeln, die Sie hier vorgenommen haben.',
        ],
        'button_final' => 'Vervollständigte Zusammenfassung herunterladen',
        'button_final-tooltip' => [
            'title' => 'Vervollständigte Zusammenfassung',
            'explain' => 'Die Daten sind die gleichen wie in der ursprünglichen Zusammenfassung, wurden jedoch mit den Ausschlüssen jedes Teilnehmers/Teilnehmerin ergänzt, sowie der Zielperson, die dieser Teilnehmer/diese Teilnehmerin während der Veranstaltung erhalten hat. Es sei denn, dies führt zu einer Blockierung, bei der keine Zielperson für jeden Teilnehmer/jede Teilnehmerin beim nächsten Mal mehr gefunden werden kann.',
            'limit' => 'Aufgrund des festgelegten Datums für die Veranstaltung ist diese Funktion nur vom {expires_at} bis zum {deleted_at} verfügbar.',
        ],
    ],
    'purge'         => [
        'button'  => 'Alle löschen',
        'confirm' => [
            'title'  => 'Sind Sie sicher, dass Sie alle Daten vor der automatischen Bereinigung am :deletion löschen möchten?',
            // Final recap available and draw not expired yet
            'body_final'   => 'Sie können die Zusammenfassung der Ziehungen für diese Veranstaltung nicht mehr herunterladen, und die Teilnehmer/innen können nicht mehr an ihren geheimen Weihnachtsmann/ihre geheime Weihnachtsfrau schreiben. Diese Aktion kann nicht rückgängig gemacht werden.',
            // Draw expired, final recap may be available or not
            'body_expired' => 'Sie können die Zusammenfassung dieser Veranstaltung nicht mehr herunterladen. Diese Aktion kann nicht rückgängig gemacht werden.',
            // No final recap available and draw not expired yet
            'body_nofinal' => 'Sie können die Zusammenfassung dieser Veranstaltung nicht mehr herunterladen, und die Teilnehmer/innen können nicht mehr an ihren geheimen Weihnachtsmann/ihre geheime Weihnachtsfrau schreiben. Diese Aktion kann nicht rückgängig gemacht werden.',
            'value'  => 'Alle Daten löschen',
            // Use !: not to be transformed by vue-i18n-generator
            'help'   => 'Bitte geben Sie "[+!:verification]" unten ein, um zu bestätigen.',
            'ok'     => 'Ok',
            'cancel' => 'Abbrechen',
        ],
    ],
    'withdraw'      => [
        'button'  => 'Entfernen',
        'confirm' => [
            'title'  => 'Sind Sie sicher, dass Sie {name} von der Veranstaltung entfernen möchten?',
            'body'   => 'Alle Nachrichten, die von seiner/ihrer Zielperson empfangen wurden, werden an seinen/ihren neuen geheimen Weihnachtsmann/ihre geheime Weihnachtsfrau weitergeleitet. Diese Aktion kann nicht rückgängig gemacht werden.',
            'value'  => 'Teilnahme abbrechen',
            // Use !: not to be transformed by vue-i18n-generator
            'help'   => 'Bitte geben Sie "[+!:verification]" unten ein, um zu bestätigen.',
            'ok'     => 'Ok',
            'cancel' => 'Abbrechen',
        ],
    ],
    'expired' => 'Ihre Veranstaltung ist abgelaufen ({expires_at}). Einige Aktionen sind nicht mehr verfügbar, wie z.B. das erneute Versenden des Namens der Zielperson an einen Teilnehmer/eine Teilnehmerin.',
];
