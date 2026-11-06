<?php

declare(strict_types=1);

return [
    'url' => 'Link, den ein(e) Teilnehmer(in) per E-Mail erhalten hat',
    'fix' => 'Korrigieren',
    'date' => [
        'label' => 'Datum der Veranstaltung',
        'help' => 'Die Daten der Veranstaltung werden am {deleted_at} gelöscht.',
    ],
    'reveal' => [
        'button' => 'Empfänger anzeigen',
        'done' => 'Empfänger angezeigt am :date. Die Teilnehmer sehen, dass die Liste eingesehen wurde.',
        'confirm' => [
            'title' => 'Anzeigen, wer wen beschenkt?',
            'body' => 'Sie sehen den Empfänger jedes Teilnehmers, nützlich bei Zustellproblemen oder für weitere Veranstaltungen. Das ist endgültig, und die Teilnehmer sehen auf ihrer Seite, dass die Liste dem Organisator angezeigt wurde.',
            'ok' => 'Anzeigen',
            'cancel' => 'Abbrechen',
        ],
    ],
    'list' => [
        'target' => 'Empfänger',
        'name' => 'Name',
        'email' => 'E-Mail-Adresse',
        'status' => 'E-Mail-Versandstatus',
        'caption' => 'Liste der Teilnehmer/innen',
        'withdraw' => 'Entfernen',
        'resend_failed' => 'Fehlgeschlagene E-Mails erneut senden (:count)',
        'received_count' => ':received/:total Teilnehmer haben den Empfang ihrer E-Mail bestätigt (Antwort, Seitenbesuch oder Empfangsbestätigung)',
    ],
    'up_and_sent' => 'Erfolgreich geändert!',
    'cannot_withdraw' => ':name kann nicht entfernt werden',
    'withdrawn' => ':name nimmt nicht mehr an der Veranstaltung teil.',
    'deleted' => 'Alle Daten wurden gelöscht',
    'download' => [
        'button' => 'Zusammenfassung herunterladen',
        'button-tooltip' => [
            'title' => 'Zusammenfassung',
            'content' => 'Dies sind die Daten, wie Sie sie bei der Erstellung der Veranstaltung eingegeben haben. Nur die E-Mail-Adressen können sich geändert haben, um Änderungen widerzuspiegeln, die Sie hier vorgenommen haben.',
        ],
        'button_initial' => 'Ursprüngliche Zusammenfassung herunterladen',
        'button_initial-tooltip' => [
            'title' => 'Ursprüngliche Zusammenfassung',
            'content' => 'Dies sind die Daten, wie Sie sie bei der Erstellung der Veranstaltung eingegeben haben. Nur die E-Mail-Adressen können sich geändert haben, um Änderungen widerzuspiegeln, die Sie hier vorgenommen haben.',
        ],
        'button_final' => 'Vervollständigte Zusammenfassung herunterladen',
        'button_final-tooltip' => [
            'title' => 'Vervollständigte Zusammenfassung',
            'explain' => 'Die Daten sind die gleichen wie in der ursprünglichen Zusammenfassung, wurden jedoch mit den Ausschlüssen jedes Teilnehmers/Teilnehmerin ergänzt, sowie der Zielperson, die dieser Teilnehmer/diese Teilnehmerin während der Veranstaltung erhalten hat. Es sei denn, dies führt zu einer Blockierung, bei der keine Zielperson für jeden Teilnehmer/jede Teilnehmerin beim nächsten Mal mehr gefunden werden kann.',
            'limit' => 'Aufgrund des festgelegten Datums für die Veranstaltung ist diese Funktion nur vom {expires_at} bis zum {deleted_at} verfügbar, oder sobald Sie die Ziele aufgedeckt haben.',
        ],
    ],
    'purge' => [
        'button' => 'Alle löschen',
        'confirm' => [
            'title' => 'Sind Sie sicher, dass Sie alle Daten vor der automatischen Bereinigung am :deletion löschen möchten?',
            // Final recap available and draw not expired yet
            'body_final' => 'Sie können die Zusammenfassung der Ziehungen für diese Veranstaltung nicht mehr herunterladen, und die Teilnehmer/innen können nicht mehr an ihren geheimen Weihnachtsmann/ihre geheime Weihnachtsfrau schreiben. Diese Aktion kann nicht rückgängig gemacht werden.',
            // Draw expired, final recap may be available or not
            'body_expired' => 'Sie können die Zusammenfassung dieser Veranstaltung nicht mehr herunterladen. Diese Aktion kann nicht rückgängig gemacht werden.',
            // No final recap available and draw not expired yet
            'body_nofinal' => 'Sie können die Zusammenfassung dieser Veranstaltung nicht mehr herunterladen, und die Teilnehmer/innen können nicht mehr an ihren geheimen Weihnachtsmann/ihre geheime Weihnachtsfrau schreiben. Diese Aktion kann nicht rückgängig gemacht werden.',
            'value' => 'Alle Daten löschen',
            // Use !: not to be transformed by vue-i18n-generator
            'help' => 'Bitte geben Sie "[+!:verification]" unten ein, um zu bestätigen.',
            'ok' => 'Ok',
            'cancel' => 'Abbrechen',
        ],
    ],
    'report' => [
        'not_me' => 'Falscher Empfänger',
        'not_participating' => 'Nimmt nicht teil',
        'help' => [
            'not_me' => 'Der Empfänger dieser E-Mail gibt an, dass sie nicht für ihn war: Korrigieren Sie die Adresse (das Ziel wurde ihm angezeigt, Sie können die Person auch entfernen und neu hinzufügen).',
            'not_participating' => 'Diese Person gibt an, nicht teilzunehmen: Sie können sie aus der Veranstaltung entfernen.',
        ],
    ],
    'edit' => [
        'warning' => 'Ein zufällig ausgewählter Teilnehmer kann ein neues Ziel bekommen. Die Auslosung fand vor {days} Tag(en) statt: Je länger das her ist, desto unangenehmer für diese Person, die ihr Geschenk vielleicht schon gekauft hat.',
        'add' => 'Hinzufügen',
        'new' => 'neu',
        'undo' => 'Rückgängig',
        'multi' => 'Mehrere Teilnehmer auf einmal ändern',
        'validate' => 'Bestätigen ({add} hinzugefügt, {remove} entfernt)',
        'cancel' => 'Änderungen verwerfen',
        'done' => 'Änderungen gespeichert, die betroffenen Teilnehmer wurden benachrichtigt.',
        'confirm_add' => [
            'title' => '{name} zur Veranstaltung hinzufügen?',
            'body' => '{name} erhält das Ziel per E-Mail, und nur eine Person bekommt ein neues Ziel (sie wird benachrichtigt). Diese Aktion kann nicht rückgängig gemacht werden.',
        ],
        'confirm_multi' => [
            'title' => 'Änderungen bestätigen?',
            'body' => '{add} hinzugefügt und {remove} entfernt. Jede Person, deren Ziel sich ändert, erhält nur eine E-Mail. Diese Aktion kann nicht rückgängig gemacht werden.',
        ],
    ],
    'withdraw' => [
        'button' => 'Entfernen',
        'confirm' => [
            'title' => 'Sind Sie sicher, dass Sie {name} von der Veranstaltung entfernen möchten?',
            'body' => 'Alle Nachrichten, die von seiner/ihrer Zielperson empfangen wurden, werden an seinen/ihren neuen geheimen Weihnachtsmann/ihre geheime Weihnachtsfrau weitergeleitet. Diese Aktion kann nicht rückgängig gemacht werden.',
            'value' => 'Teilnahme abbrechen',
            // Use !: not to be transformed by vue-i18n-generator
            'help' => 'Bitte geben Sie "[+!:verification]" unten ein, um zu bestätigen.',
            'ok' => 'Ok',
            'cancel' => 'Abbrechen',
        ],
    ],
    'expired' => 'Ihre Veranstaltung ist abgelaufen ({expires_at}). Einige Aktionen sind nicht mehr verfügbar, wie z.B. das erneute Versenden des Namens der Zielperson an einen Teilnehmer/eine Teilnehmerin.',
];
