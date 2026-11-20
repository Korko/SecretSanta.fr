<?php

declare(strict_types=1);

return [
    'inputEdit' => [
        'update' => 'Edit',
        'updating' => 'Saving',
        'updated' => 'Change saved',
        'submit' => 'Confirm',
        'cancel' => 'Cancel',
        'error' => 'Error: :error',
        'redo' => 'Try again',
    ],

    'nav' => [
        'what' => 'What is it?',
        'how' => 'How does it work?',
        'go' => 'Let\'s go!',
        'faq' => 'Frequently Asked Questions',
    ],

    'title' => 'Secret Santa',
    'subtitle' => 'Give each other gifts... secretly!',

    'fyi' => 'For your information',

    'section' => [
        'what' => [
            'title' => 'What is it?',
            'subtitle' => 'Secret Santa explained',
            'heading1' => 'The idea',
            'content1' => "Secret Santa is a fun and original way to exchange gifts anonymously between friends, colleagues...
It's simple: each participant randomly receives the name of the person they will give a gift to.
The gift amount is usually set beforehand ($2, $5, $10...)
The goal is not necessarily to give a fancy gift but to be creative!",
            'notice' => 'SecretSanta is completely free and ad-free.
Everything is paid for by the developer himself.
If you like this tool, consider making a donation.
:button',
        ],
        'how' => [
            'title' => 'How does it work?',
            'subtitle' => 'You\'ll see, it\'s very simple!',
            'heading1' => 'Step one: list the participants',
            'content1' => 'With the "Add a participant" and "Remove" buttons, you can adjust the number of people.
For each person, enter a name or nickname, and an email address.
Two participants cannot have the same name, otherwise they cannot be told apart.
Note that SecretSanta is designed so that nobody can draw themselves.',
            'heading2' => 'Step two: set the exclusions',
            'content2' => "Add exclusions. If you don't want two participants to be able to draw each other, fill in the \"Exclusions\" field.",
            'heading3' => 'Step three: prepare the email',
            'content3' => "All that's left is to fill in the title and body of the email the participants will receive.
The \"{TARGET}\" keyword is mandatory in the message body, it will automatically be replaced by the name of each participant's \"target\".
(Optional) You can also use the \"{SANTA}\" keyword, which will be replaced by the name of the email recipient.
For example, you could include the sentence \"Hello {SANTA}, you will give a gift to {TARGET}\".",
            'heading4' => 'And then?',
            'content4' => 'Until the event date set at the end, participants can write a note to their Santa from a link they receive by email.
Santa, however, cannot reply, as that would reveal their identity.
The organizer also has a dedicated interface to find the summary of participants and exclusions.',
            'notice' => 'SecretSanta only stores your data when required.
It is encrypted so that it is unusable without action on your part.
None of this data will be shared and you have full control over it.
The source code is available on :link',
        ],
        'go' => [
            'title' => 'Your turn!',
            'subtitle' => 'Fill in, click and off you go!',
        ],
    ],

    'waiting' => 'Form loading. If this message stays displayed, try refreshing the page, otherwise contact me by email (<a href="mailto:&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;">&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;</a>) or via <a href="https://github.com/Korko">GitHub</a>. Thank you.',

    'success' => 'Sent successfully!',

    'organizerIn' => 'The organizer takes part',
    'organizerOut' => 'The organizer does not take part',

    'organizer' => [
        'title' => 'Organizer details',
        'name' => 'Organizer name or nickname',
        'email' => 'Organizer email address',
    ],

    'participants' => [
        'title' => 'Participant details',
        'import' => 'Import from a file',
        'importing' => 'Importing',
        'caption' => 'List of participants',
    ],

    'participant' => [
        'organizer' => 'Organizer',
        'name' => [
            'label' => 'Name or nickname',
            'placeholder' => 'e.g. Paul or Korko',
        ],
        'email' => [
            'label' => 'Email address',
            'placeholder' => 'e.g. michael@aol.com',
            'suggest' => 'Did you mean :email?',
        ],
        'exclusions' => [
            'label' => 'Exclusions',
            'placeholder' => 'No exclusion',
            'noOptions' => 'Empty list',
            'noResult' => 'No result',
            'both' => 'Both ways: click to apply one way only',
            'oneWay' => 'One way only: click to apply both ways',
            'derived' => 'Added from :name\'s row',
            'remove' => 'Remove the exclusion :name',
            'help' => 'An exclusion applies both ways: the arrow on each tag limits it to one way (imported ones keep the file\'s direction).',
        ],
        'remove' => 'Remove',
        'add' => 'Add a participant',
    ],

    'csv' => [
        'title' => 'Import a list of participants from a CSV file',
        'help' => 'How to create a CSV file with :excel Microsoft Office Excel :elink or :calc LibreOffice Calc :elink',
        'format' => 'For your CSV file to work, here is the expected format:',
        'column1' => 'Participant name',
        'column2' => 'Email address',
        'column3' => 'Exclusions (comma-separated names)',
        'warning' => 'Warning, importing this data will remove the participants already entered.',
        'cancel' => 'Cancel',
        'import' => 'Import',
        'importError' => 'An error occurred during the import.',
        'importSuccess' => 'The import was successful.',
        'analyzing' => 'Loading...',
    ],

    'mail' => [
        'title' => [
            'label' => 'Email title',
            'placeholder' => 'e.g. Secret santa party on December 23rd at Martin\'s, {SANTA} your target is...',
        ],
        'content' => [
            'label' => 'Email content',
            'placeholder' => 'e.g. Hi {SANTA}, for the secret santa party, your target is {TARGET}. Reminder: the gift amount is $3!',
            'tip1' => 'Use ":santa&#123;SANTA&#125;:close" for the name of the email recipient and ":target&#123;TARGET&#125;:close" for the name of their target.',
            'tip2' => 'Tip: remember to mention the date, the place and the gift amount.',
        ],
        'post' => '----
To write to your Secret Santa, go to the following page: :link
via SecretSanta',
    ],

    'data-expiration' => 'Event date: ',
    'data-expiration-tooltip' => [
        'title' => 'Event date',
        'interface' => 'A dedicated interface will let you access a summary of the participants until the day of the event.',
        'deletion' => 'All stored data will be deleted one week later.',
    ],

    'submit' => 'Draw!',

    'paypal' => [
        'alt' => 'PayPal, the safer way to pay online',
    ],

    'internalError' => 'Internal error',
];
