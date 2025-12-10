<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted' => 'Das Feld :attribute muss akzeptiert werden.',
    'active_url' => 'Das Feld :attribute ist keine gültige URL.',
    'after' => 'Das Feld :attribute muss ein Datum nach dem :date sein.',
    'alpha' => 'Das Feld :attribute darf nur Buchstaben enthalten.',
    'alpha_dash' => 'Das Feld :attribute darf nur Buchstaben, Zahlen und Bindestriche enthalten.',
    'alpha_num' => 'Das Feld :attribute darf nur Zahlen und Buchstaben enthalten.',
    'array' => 'Das Feld :attribute muss ein Array sein.',
    'before' => 'Das Feld :attribute muss ein Datum vor dem :date sein.',
    'between' => [
        'numeric' => 'Der Wert von :attribute muss zwischen :min und :max liegen.',
        'file' => 'Die Datei :attribute muss zwischen :min und :max Kilobyte groß sein.',
        'string' => 'Der Text :attribute muss zwischen :min und :max Zeichen lang sein.',
        'array' => 'Das Array :attribute muss zwischen :min und :max Elemente enthalten.',
    ],
    'boolean' => 'Das Feld :attribute muss wahr oder falsch sein.',
    'confirmed' => 'Das Bestätigungsfeld :attribute stimmt nicht überein.',
    'date' => 'Das Feld :attribute ist kein gültiges Datum.',
    'date_format' => 'Das Feld :attribute entspricht nicht dem Format :format.',
    'different' => 'Die Felder :attribute und :other müssen unterschiedlich sein.',
    'digits' => 'Das Feld :attribute muss :digits Ziffern enthalten.',
    'digits_between' => 'Das Feld :attribute muss zwischen :min und :max Ziffern enthalten.',
    'email' => 'Das Feld :attribute muss eine gültige E-Mail-Adresse sein.',
    'exists' => 'Das ausgewählte Feld :attribute ist ungültig.',
    'filled' => 'Das Feld :attribute ist erforderlich.',
    'image' => 'Das Feld :attribute muss ein Bild sein.',
    'in' => 'Das Feld :attribute ist ungültig.',
    'integer' => 'Das Feld :attribute muss eine ganze Zahl sein.',
    'ip' => 'Das Feld :attribute muss eine gültige IP-Adresse sein.',
    'json' => 'Das Feld :attribute muss ein gültiges JSON-Dokument sein.',
    'max' => [
        'numeric' => 'Der Wert von :attribute darf nicht größer als :max sein.',
        'file' => 'Die Datei :attribute darf nicht größer als :max Kilobyte sein.',
        'string' => 'Der Text von :attribute darf nicht mehr als :max Zeichen enthalten.',
        'array' => 'Das Array :attribute darf nicht mehr als :max Elemente enthalten.',
    ],
    'mimes' => 'Das Feld :attribute muss eine Datei vom Typ : :values sein.',
    'min' => [
        'numeric' => 'Der Wert von :attribute muss größer als :min sein.',
        'file' => 'Die Datei :attribute muss größer als :min Kilobyte sein.',
        'string' => 'Der Text von :attribute muss mindestens :min Zeichen enthalten.',
        'array' => 'Das Array :attribute muss mindestens :min Elemente enthalten.',
    ],
    'not_in' => 'Das ausgewählte Feld :attribute ist ungültig.',
    'numeric' => 'Das Feld :attribute muss eine Zahl sein.',
    'regex' => 'Das Format des Feldes :attribute ist ungültig.',
    'required' => 'Das Feld :attribute ist erforderlich.',
    'required_if' => 'Das Feld :attribute ist erforderlich, wenn :other den Wert :value hat.',
    'required_unless' => 'Das Feld :attribute ist erforderlich, es sei denn, :other ist in :values enthalten.',
    'required_with' => 'Das Feld :attribute ist erforderlich, wenn :values vorhanden ist.',
    'required_with_all' => 'Das Feld :attribute ist erforderlich, wenn :values vorhanden sind.',
    'required_without' => 'Das Feld :attribute ist erforderlich, wenn :values nicht vorhanden ist.',
    'required_without_all' => 'Das Feld :attribute ist erforderlich, wenn keines der Felder :values vorhanden ist.',
    'same' => 'Die Felder :attribute und :other müssen übereinstimmen.',
    'size' => [
        'numeric' => 'Der Wert von :attribute muss :size sein.',
        'file' => 'Die Datei von :attribute muss :size Kilobyte groß sein.',
        'string' => 'Der Text von :attribute muss :size Zeichen lang sein.',
        'array' => 'Das Array :attribute muss :size Elemente enthalten.',
    ],
    'string' => 'Das Feld :attribute muss eine Zeichenkette sein.',
    'timezone' => 'Das Feld :attribute muss eine gültige Zeitzone sein.',
    'unique' => 'Der Wert des Feldes :attribute wird bereits verwendet.',
    'url' => 'Das Format der URL von :attribute ist ungültig.',

    'recaptcha' => 'Das Captcha konnte nicht validiert werden.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'g-recaptcha-response' => [
            'required' => 'Das Captcha ist erforderlich.',
            'recaptcha' => 'Das Captcha ist ungültig.',
        ],
        'randomform' => [
            'participant-organizer' => [
                'required' => 'Sie müssen angeben, ob der Organisator/die Organisatorin an der Veranstaltung teilnimmt oder nicht.',
            ],
            'title' => [
                'required' => 'Der Betreff der E-Mail ist erforderlich.',
            ],
            'content' => [
                'required' => 'Der Inhalt der E-Mail ist erforderlich.',
                'contains' => 'Der Inhalt der E-Mail muss das Wort {TARGET} enthalten, um das Ziel anzugeben.',
            ],
            'expiration' => [
                'required' => 'Das Ablaufdatum ist erforderlich.',
                'min' => 'Das Ablaufdatum kann nicht vor morgen liegen.',
                'max' => 'Das Ablaufdatum darf nicht mehr als ein Jahr in der Zukunft liegen.',
                'format' => 'Das Ablaufdatum muss dem Format Jahr-Monat-Tag entsprechen, z.B. 2022-02-05.',
            ],
            'participants' => [
                'length' => 'Es müssen mindestens 3 Teilnehmer(innen) vorhanden sein.',
            ],
            'organizer' => [
                'name' => [
                    'required' => 'Der Name des Organisators/der Organisatorin ist erforderlich.',
                ],
                'email' => [
                    'required' => 'Diese E-Mail-Adresse ist erforderlich.',
                    'format' => 'Das Format dieser Adresse ist ungültig.',
                ],
            ],
            'participant' => [
                'name' => [
                    'required' => 'Dieser Teilnehmer/diese Teilnehmerin ist erforderlich (mindestens 3 Personen).',
                    'distinct' => 'Dieser Teilnehmer/diese Teilnehmerin hat keinen einzigartigen Namen.',
                ],
                'email' => [
                    'required' => 'Diese E-Mail-Adresse ist erforderlich.',
                    'format' => 'Das Format dieser Adresse ist ungültig.',
                ],
            ],
        ],
        'dearSanta' => [
            'content' => [
                'required' => 'Der Inhalt der Nachricht ist erforderlich.',
            ],
        ],
        'organizer' => [
            'email' => [
                'required' => 'Die neue Adresse ist erforderlich.',
                'format' => 'Das Format der Adresse ist ungültig.',
            ],
        ],
        'fixOrganizer' => [
            'url' => [
                'required' => 'Die URL ist erforderlich.',
                'format' => 'Die URL ist ungültig.',
            ],
            'email' => [
                'required' => 'Die E-Mail-Adresse ist erforderlich.',
                'format' => 'Das Format der Adresse ist ungültig.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name' => 'Name',
        'username' => 'Benutzername',
        'email' => 'E-Mail',
        'first_name' => 'Vorname',
        'last_name' => 'Nachname',
        'password' => 'Passwort',
        'password_confirmation' => 'Passwort bestätigung',
        'city' => 'Stadt',
        'country' => 'Land',
        'address' => 'Adresse',
        'phone' => 'Telefon',
        'mobile' => 'Mobiltelefon',
        'age' => 'Alter',
        'sex' => 'Geschlecht',
        'gender' => 'Geschlecht',
        'day' => 'Tag',
        'month' => 'Monat',
        'year' => 'Jahr',
        'hour' => 'Stunde',
        'minute' => 'Minute',
        'second' => 'Sekunde',
        'title' => 'Titel',
        'content' => 'Inhalt',
        'description' => 'Beschreibung',
        'excerpt' => 'Auszug',
        'date' => 'Datum',
        'time' => 'Uhrzeit',
        'available' => 'Verfügbar',
        'size' => 'Größe',
        'g-recaptcha-response' => 'Recaptcha',
    ],
];
