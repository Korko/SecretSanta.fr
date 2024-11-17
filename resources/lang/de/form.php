<?php

return [
    'inputEdit' => [
        'update' => 'Ändern',
        'updating' => 'Speichern läuft',
        'updated' => 'Änderung vorgenommen',
        'submit' => 'Bestätigen',
        'cancel' => 'Abbrechen',
        'error' => 'Fehler: :error',
        'redo' => 'Erneut versuchen',
    ],

    'nav' => [
        'what' => 'Was ist das?',
        'how'  => 'Wie funktioniert das?',
        'go'   => 'Los geht\'s!',
        'faq'  => 'Häufig gestellte Fragen',
    ],

    'title'    => 'SecretSanta',
    'subtitle' => 'Verschenken Sie Geschenke... geheim!',

    'fyi' => 'Zur Information',

    'section' => [
        'what' => [
            'title'    => 'Was ist das?',
            'subtitle' => 'Beschreibung von Secret Santa',
            'heading1' => 'Das Prinzip',
            'content1' => "Secret Santa ist eine lustige und originelle Möglichkeit, sich gegenseitig anonym Geschenke zu machen – unter Freunden, Kollegen...
            Der Ablauf ist einfach: Jede(r) Teilnehmer(in) erhält zufällig den Namen der Person, der er ein Geschenk machen soll.
            Der Wert des Geschenks wird in der Regel vorher festgelegt (2 €, 5 €, 10 €...).
            Es geht nicht unbedingt darum, ein tolles Geschenk zu machen, sondern kreativ zu sein!",
            'notice' => 'secretsanta.fr ist völlig kostenlos und ohne Werbung.
            Alles wird vom Entwickler selbst bezahlt.
            Wenn Ihnen dieses Tool gefällt, denken Sie bitte daran, eine Spende zu machen.
            :button',
        ],
        'how' => [
            'title'    => 'Wie funktioniert das?',
            'subtitle' => 'Es ist ganz einfach!',
            'heading1' => 'Erster Schritt: Teilnehmerliste erstellen',
            'content1' => "Mit den Schaltflächen \"Teilnehmer(in) hinzufügen\" und \"Teilnehmer(in) entfernen\" können Sie die Anzahl der Personen anpassen.
Geben Sie für jede Person einen Namen/Vornamen oder ein Pseudonym sowie eine E-Mail-Adresse ein.
Zwei Teilnehmer(innen) dürfen nicht denselben Namen haben, andernfalls können sie nicht unterschieden werden.
Beachten Sie, dass secretsanta.de so konzipiert ist, dass niemand sich selbst ziehen kann.",
            'heading2' => 'Zweiter Schritt: Ausschlüsse festlegen',
            'content2' => "Fügen Sie Ausschlüsse hinzu. Wenn Sie nicht möchten, dass zwei Teilnehmer(innen) einander ziehen, füllen Sie das Feld \"Ausschlüsse\" aus.",
            'heading3' => 'Dritter Schritt: E-Mail vorbereiten',
            'content3' => "Nun müssen Sie nur noch den Betreff und den Inhalt der E-Mail ausfüllen, die die Teilnehmer(innen) erhalten werden.
Das Schlüsselwort \"{TARGET}\" ist im Nachrichtentext erforderlich, damit jeder seine \"Zielperson\" erhält.
(Optional) Sie können auch das Schlüsselwort \"{SANTA}\" verwenden, das durch den Namen des Empfängers der E-Mail ersetzt wird.",
            'notice' => 'secretsanta.fr speichert Ihre Daten nur, wenn es notwendig ist.
Diese werden verschlüsselt, sodass sie ohne Ihr Zutun unbrauchbar sind.
Keine dieser Daten werden geteilt, und Sie haben die vollständige Kontrolle darüber.
Der Quellcode ist unter :link verfügbar.',
            'heading4' => 'Und danach?',
            'content4' => "Bis zum festgelegten Datum der Veranstaltung können die Teilnehmer(innen) ihrem Santa eine Nachricht über einen Link senden, den sie per E-Mail erhalten.
Dieser kann jedoch nicht antworten, um seine Identität nicht zu enthüllen.
Der Organisator hat auch eine dedizierte Oberfläche, um eine Zusammenfassung der Teilnehmer(innen) und Ausschlüsse zu finden.",
            'notice' => 'secretsanta.fr speichert Ihre Daten nur, wenn es notwendig ist.
Diese werden verschlüsselt, sodass sie ohne Ihr Zutun unbrauchbar sind.
Keine dieser Daten werden geteilt, und Sie haben die vollständige Kontrolle darüber.
Der Quellcode ist unter :link verfügbar.',
        ],
        'go' => [
            'title'    => 'Jetzt sind Sie dran!',
            'subtitle' => 'Ausfüllen, klicken und los geht\'s!',
        ],
    ],

    'waiting' => 'Formular wird erstellt. Wenn diese Nachricht weiterhin angezeigt wird, versuchen Sie, die Seite zu aktualisieren, oder kontaktieren Sie mich per E-Mail (<a href="mailto:&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;">&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;</a>) oder über <a href="https://github.com/Korko">GitHub</a>. Vielen Dank.',

    'success' => 'Erfolgreich gesendet!',

    'organizerIn' => 'Organisator(in) nimmt teil',
    'organizerOut' => 'Organisator(in) nimmt nicht teil',

    'organizer' => [
        'title' => 'Details des Organisators',
        'name'  => 'Name oder Pseudonym des Organisators',
        'email' => 'E-Mail-Adresse des Organisators',
    ],

    'participants' => [
        'title'     => 'Details der Teilnehmer(innen)',
        'import'    => 'Von einer Datei importieren',
        'importing' => 'Import läuft',
        'caption'   => 'Teilnehmerliste',
    ],

    'participant' => [
        'organizer'  => 'Organisator(in)',
        'name'       => [
            'label'       => 'Name oder Pseudonym',
            'placeholder' => 'z.B. Paul oder Korko',
        ],
        'email'      => [
            'label'       => 'E-Mail-Adresse',
            'placeholder' => 'z.B. michel@aol.com',
        ],
        'exclusions' => [
            'label'       => 'Ausschlüsse',
            'placeholder' => 'Keine Ausschlüsse',
            'noOptions'   => 'Liste leer',
            'noResult'    => 'Kein Ergebnis',
        ],
        'remove'     => 'Teilnehmer(in) entfernen',
        'add'        => 'Teilnehmer(in) hinzufügen',
    ],

    'csv' => [
        'title'         => 'Teilnehmerliste aus einer CSV-Datei importieren',
        'help'          => 'Wie man eine CSV-Datei mit :excel Microsoft Office Excel :elink oder :calc Libre Office Calc :elink erstellt',
        'format'        => 'Damit Ihre CSV-Datei funktioniert, ist folgendes Format erforderlich:',
        'column1'       => 'Name des Teilnehmers',
        'column2'       => 'E-Mail-Adresse',
        'column3'       => 'Ausschlüsse (durch Kommas getrennt)',
        'warning'       => 'Achtung, der Import dieser Daten löscht bereits eingegebene Teilnehmer.',
        'cancel'        => 'Abbrechen',
        'import'        => 'Importieren',
        'importError'   => 'Beim Import ist ein Fehler aufgetreten.',
        'importSuccess' => 'Der Import war erfolgreich.',
        'analyzing'     => 'Lädt...',
    ],

    'mail' => [
        'title' => [
            'label'       => 'Betreff der E-Mail',
            'placeholder' => 'z.B. Secret Santa Party am 23. Dezember bei Martin, {SANTA}, dein Ziel ist...',
        ],
        'content' => [
            'label'       => 'Inhalt der E-Mail',
            'placeholder' => 'z.B. Hallo {SANTA}, bei der Secret Santa Party ist dein Ziel {TARGET}. Denke daran, das Geschenk sollte 3 € kosten!',
            'tip1' => 'Verwende ":santa&#123;SANTA&#125;:close" für den Namen des Empfängers und ":target&#123;TARGET&#125;:close" für den Namen seiner Zielperson.',
            'tip2' => 'Tipp: Denke daran, das Datum, den Ort und den Wert des Geschenks zu erwähnen.',
        ],
        'post' => '----
Um deinem Secret Santa zu schreiben, besuche folgende Seite: :link
via SecretSanta.fr',
    ],

    'data-expiration' => 'Datum der Veranstaltung: ',
    'data-expiration-tooltip' => [
        'title' => 'Datum der Veranstaltung',
        'interface' => 'Eine dedizierte Oberfläche ermöglicht es Ihnen, bis zum Veranstaltungstag eine Zusammenfassung der Teilnehmer und Ausschlüsse zu finden.',
        'deletion' => 'Alle gespeicherten Daten werden eine Woche nach dem Ereignis gelöscht.',
    ],

    'submit'  => 'Starte die Zufallsziehung!',

    'paypal' => [
        'alt' => 'PayPal, der sichere Zahlungsweg online',
    ],

    'internalError' => 'Interner Fehler',
];