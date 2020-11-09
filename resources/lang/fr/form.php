<?php

return [
    'nav' => [
        'what' => 'Qu\'est-ce que c\'est ?',
        'how'  => 'Comment faire ?',
        'go'   => 'Allez, c\'est parti !',
        'faq'  => 'Foire Aux Questions',
    ],

    'title'    => 'Secret Santa .fr',
    'subtitle' => 'Offrez-vous des cadeaux... secrètement !',

    'fyi' => 'Pour votre information',

    'section' => [
        'what' => [
            'title'    => 'Qu\'est-ce que c\'est ?',
            'subtitle' => 'Description du Secret Santa',
            'heading1' => 'Le principe',
            'content1' => "Secret Santa est un moyen drôle et original de s'offrir anonymement des cadeaux entre amis, collègues...
Le déroulement est simple : chaque participant reçoit, de façon aléatoire, le nom de la personne à qui il devra faire un cadeau.
Le montant du cadeau est généralement fixé au préalable (2€, 5€, 10€...)
Le but n'est pas forcément de faire un beau cadeau mais d'être créatif !",
            'notice' => 'secretsanta.fr est entièrement gratuit et sans publicité.
Tout est payé par le développeur lui-même.
Si cet outil vous plait, pensez à faire un don.
:button',
        ],
        'how' => [
            'title'    => 'Comment faire ?',
            'subtitle' => 'Vous allez voir, c\'est très simple !',
            'heading1' => 'Première étape : lister les participants',
            'content1' => "Grâce aux boutons \"Ajouter un participant\" et \"Enlever un participant\", il est possible d'ajuster le nombre de personnes.
Pour chaque personne, indiquez un nom/prénom ou un pseudonyme, et une adresse email. Deux participants ne peuvent avoir le même nom, sinon il est impossible de les différencier.
A noter que secretsanta.fr est conçu de façon à ce qu'une personne ne puisse pas se piocher elle-même.",
            'heading2' => 'Deuxième étape : préciser les exclusions',
            'content2' => "Ajoutez des exclusions. Si vous ne voulez pas que deux participants puissent se piocher l'un l'autre, remplissez le champ \"Exclusions\".",
            'heading3' => 'Troisième étape : préparer l\'e-mail',
            'content3' => "Il ne vous reste plus qu'à remplir le titre et le corps du courriel que les participants recevront.
Le mot clef \"{TARGET}\" est obligatoire dans le corps du message afin de donner à chaque personne sa \"cible\".
(Optionel) Vous pouvez aussi utiliser le mot clef \"{SANTA}\" qui sera remplacé par le nom du destinataire du message.",
            'notice' => 'secretsanta.fr ne sauvegarde vos données que lorsque cela est requis.
Celles-ci sont chiffrées pour être inutilisables sans action de votre part.
Aucune de ces données ne seront partagées et vous avez le contrôle total sur celles-ci.
Le code source est disponible sur :link',
            'heading4' => 'Et après ?',
            'content4' => "Jusqu'au jour de l'évènement spécifiée à la fin, les participants peuvent écrire un mot à leur Santa depuis un lien qu'ils reçoivent par email. Mais celui-ci ne peut pas répondre, au risque de dévoiler son identité.
L'organisateur dispose aussi d'une interface dédiée pour retrouver le récapitulatif des participants et des exclusions.",
            'notice' => 'secretsanta.fr ne sauvegarde vos données que lorsque cela est requis.
Celles-ci sont chiffrées pour être inutilisables sans action de votre part.
Aucune de ces données ne seront partagées et vous avez le contrôle total sur celles-ci.
Le code source est disponible sur :link',
        ],
        'go' => [
            'title'    => 'À vous de jouer !',
            'subtitle' => 'Remplissez, cliquez et c\'est parti !',
        ],
    ],

    'waiting' => 'Formulaire en cours de création. Si ce message reste affiché, essayez de rafraichir la page, sinon contactez moi par mail (<a href="mailto:&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;">&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;</a>) ou via <a href="https://github.com/Korko">GitHub</a>. Merci.',

    'success' => 'Envoyé avec succès !',

    'participants' => [
        'title'     => 'Détails des participants',
        'import'    => 'Importer depuis un fichier',
        'importing' => 'Import en cours',
        'caption'   => 'Liste des participats',
    ],

    'participant' => [
        'organizer'  => 'Organisateur',
        'name'       => [
            'label'       => 'Nom ou pseudonyme',
            'placeholder' => 'exemple : Paul ou Korko',
        ],
        'email'      => [
            'label'       => 'Adresse e-mail',
            'placeholder' => 'exemple : michel@aol.com',
        ],
        'exclusions' => [
            'label'       => 'Exclusions',
            'placeholder' => 'Aucune exclusion',
            'noOptions'   => 'Liste vide',
            'noResult'    => 'Aucun résultat',
        ],
        'remove'     => 'Enlever',
        'add'        => 'Ajouter un participant',
    ],

    'csv' => [
        'title'         => 'Importer une liste de participants depuis un fichier CSV',
        'help'          => 'Comment créer un fichier CSV avec :excel Microsoft Office Excel :elink ou :calc Libre Office Calc :elink',
        'format'        => 'Afin que votre fichier CSV fonctionne, voici le format attendu :',
        'column1'       => 'Nom du participant',
        'column2'       => 'Adresse e-mail',
        'column3'       => 'Exclusions (noms séparés par une virgule)',
        'warning'       => 'Attention, l\'import de ces données supprimera les participants déjà renseignés.',
        'cancel'        => 'Annuler',
        'import'        => 'Importer',
        'importError'   => 'Une erreur est survenue lors de l\'import.',
        'importSuccess' => 'L\'import a été effectué avec succès.',
    ],

    'mail' => [
        'title' => [
            'label'       => 'Titre du mail',
            'placeholder' => 'ex : Soirée secretsanta du 23 décembre chez Martin, {SANTA} ta cible est...',
        ],
        'content' => [
            'label'       => 'Contenu du mail',
            'placeholder' => 'ex : Salut {SANTA}, pour la soirée secret santa, ta cible c\'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !',
            'tip1' => 'Utilisez ":santa&#123;SANTA&#125;:close" pour le nom de celui qui recevra le mail et ":target&#123;TARGET&#125;:close" pour le nom de sa cible.',
            'tip2' => 'Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau.',
        ],
        'post' => '----
Pour écrire à votre Secret Santa, allez sur la page suivante : :link
via SecretSanta.fr',
    ],

    'data-expiration' => 'Date de l\'évènement : ',
    'data-expiration-tooltip' => '<h3>Date de l\'évènement</h3><ul><li>Une interface dédiée vous permettra d\'accéder à un récapitulatif des participants jusqu\'au jour de l\'évènement.</li><li>Toutes les données stockées seront supprimées une semaine après.</li></ul>',

    'submit'  => 'Lancez l\'aléatoire !',

    'paypal' => [
        'alt' => 'PayPal, le réflexe sécurité pour payer en ligne',
    ],
];
