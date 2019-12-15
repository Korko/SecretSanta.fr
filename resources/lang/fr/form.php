<?php

return [
    'nav.what' => 'Qu\'est-ce que c\'est ?',
    'nav.how'  => 'Comment faire ?',
    'nav.go'   => 'Allez, c\'est parti !',

    'title'    => 'Secret Santa . fr',
    'subtitle' => 'Offrez-vous des cadeaux... secrètement !',

    'section.what.title'    => 'Qu\'est-ce que c\'est ?',
    'section.what.subtitle' => 'Description du Secret Santa',
    'section.what.heading1' => 'Le principe',
    'section.what.content1' => "Secret Santa est un moyen drôle et original de s'offrir anonymement des cadeaux entre amis, collègues...
Le déroulement est simple : chaque participant reçoit, de façon aléatoire, le nom de la personne à qui il devra faire un cadeau.
Le montant du cadeau est généralement fixé au préalable (2€, 5€, 10€...)
Le but n'est pas forcément de faire un beau cadeau mais d'être créatif !",
    'section.what.notice' => 'Pour votre information
secretsanta.fr est entièrement gratuit et sans publicité.
Tout est payé par le développeur lui-même.
Si cet outil vous plait, pensez à faire un don.
:button',

    'section.how.title'    => 'Comment faire ?',
    'section.how.subtitle' => 'Vous allez voir, c\'est simple !',
    'section.how.heading1' => 'Première étape : spécifier le nombre et les noms des participants',
    'section.how.content1' => "Grâce aux boutons \"Ajouter un participant\" et \"Enlever un participant\", il est possible d'ajuster le nombre de personnes.
Pour chaque personne, indiquez un nom/prénom ou un pseudonyme. Deux participants ne peuvent avoir le même nom, sinon il est impossible de les différencier.
A noter que secretsanta.fr est conçu de façon à ce qu'une personne ne puisse pas se piocher elle-même.",
    'section.how.heading2' => 'Deuxième étape : remplir les informations de contact et les exclusions',
    'section.how.content2' => "Vous avez la possibilité de choisir si les participants recevront le nom de leur cible par e-mail, par sms, ou les deux.
Pour cela, précisez pour chacun une adresse e-mail et/ou un numéro de téléphone portable.
(Optionel) Ajoutez des exclusions. Si vous ne voulez pas que deux participants puissent se piocher l'un l'autre, remplissez le champ \"Exclusions\".",
    'section.how.heading3' => 'Troisième étape : préparer le mail ou le sms',
    'section.how.content3' => "Il ne vous reste plus qu'à remplir le titre et le corps du courriel ou du SMS que les participants recevront.
Le mot clef \"{TARGET}\" est obligatoire dans le corps du message afin de donner à chaque personne sa \"cible\".
(Optionel) Vous pouvez aussi utiliser le mot clef \"{SANTA}\" qui sera remplacé par le nom du destinataire du message.",
    'section.how.notice' => 'Pour votre information
secretsanta.fr ne sauvegarde vos données que lorsque cela est requis.
Celles-ci sont chiffrées pour être inutilisables sans action de votre part.
Aucune de ces données ne seront partagées et vous avez le contrôle total sur celles-ci.
Le code source est disponible sur :link',

    'section.go.title'    => 'À vous de jouer !',
    'section.go.subtitle' => 'Remplissez, cliquez et c\'est parti !',

    'waiting' => 'Formulaire en cours de création. Si ce message reste affiché, essayez de rafraichir la page, sinon contactez moi par mail (<a href="mailto:&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;">&#x6a;&#x65;&#x72;&#x65;&#x6d;&#x79;&#x2e;&#x6c;&#x65;&#x6d;&#x65;&#x73;&#x6c;&#x65;&#x40;&#x6b;&#x6f;&#x72;&#x6b;&#x6f;&#x2e;&#x66;&#x72;</a>) ou via <a href="https://github.com/Korko">GitHub</a>. Merci.',

    'success' => 'Envoyé avec succès !',

    'participants' => 'Détails des participants',

    'participant.name'       => 'Nom ou pseudonyme',
    'participant.email'      => 'Adresse e-mail',
    'participant.phone'      => 'Téléphone',
    'participant.exclusions' => 'Exclusions',

    'name.placeholder'       => 'exemple : Paul ou Korko',
    'email.placeholder'      => 'exemple : michel@aol.com',
    'phone.placeholder'      => 'ex : 0612345678',
    'exclusions.placeholder' => 'Aucune exclusion',

    'participant.remove' => 'Enlever',
    'participant.add'    => 'Ajouter un participant',

    'participants.import'    => 'Importer depuis un fichier',
    'participants.importing' => 'Import en cours',

    'csv.title'   => 'Importer une liste de participants depuis un fichier CSV',
    'csv.help'    => 'Comment créer un fichier CSV avec :excel Microsoft Office Excel :elink ou :calc Libre Office Calc :elink',
    'csv.format'  => 'Afin que votre fichier CSV fonctionne, voici le format attendu :',
    'csv.column1' => 'Nom du participant',
    'csv.column2' => 'Adresse e-mail',
    'csv.column3' => 'Numéro de téléphone',
    'csv.warning' => 'Attention, l\'import de ces données supprimera les participants déjà renseignés.',
    'csv.cancel'  => 'Annuler',
    'csv.import'  => 'Importer',

    'mail.title'             => 'Titre du mail',
    'mail.title.placeholder' => 'ex : Soirée secretsanta du 23 décembre chez Martin',

    'mail.content'             => 'Contenu du mail',
    'mail.content.placeholder' => 'ex : Salut {SANTA}, pour la soirée secret santa, ta cible c\'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !',
    'mail.post'                => '----
via SecretSanta.fr',
    'mail.post2' => '----
Pour écrire à votre Secret Santa, allez sur la page suivante : :link
via SecretSanta.fr',
    'mail.content.tip1' => 'Utilisez "{SANTA}" pour le nom de celui qui recevra le mail et "{TARGET}" pour le nom de sa cible.',
    'mail.content.tip2' => 'Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau.',

    'mail-sms' => 'et/ou',

    'sms.content'             => 'Contenu du sms :span (:left caractères restants) :espan',
    'sms.content.multiple'    => 'Contenu des :count sms :span (:left caractères restants) :espan',
    'sms.content.placeholder' => 'ex : Salut {SANTA}, pour la soirée secret santa du 23 décembre chez Martin, ta cible c\'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !',
    'sms.content.tip1'        => 'Utilisez "{SANTA}" pour le nom de celui qui recevra le sms et "{TARGET}" pour le nom de sa cible.',
    'sms.content.tip2'        => 'Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau.',

    'dearsanta'         => 'Autoriser les participants à écrire un mail à leur secret santa',
    'dearsanta.warning' => 'Cette option implique que chaque participant dispose d\'une adresse mail remplie',
    'dearsanta.limit'   => 'Date limite de stockage des emails',

    'submit'  => 'Lancez l\'aléatoire !',
    'sending' => 'Calcul en cours',
    'sent'    => 'Envoyé',

    'warning.sms_disabled' => 'L\'envoi de SMS a été temporairement désactivé car cette fonctionnalité coûte cher au développeur (environ 15€/jour et plus de 100€ pour la période hiver 2018). Date de dernière réactivation : 13 décembre 2019 à 12:30',
    'warning.give'         => 'Pour information, l\'envoi de SMS pour un tirage au sort classique de 10 personnes coûte environ 1€.
Pour que ce service continue à vivre gratuitement et sans aucune exploitation de vos données personnelles, pensez à faire un don.
:button',

    'paypal.alt' => 'PayPal, le réflexe sécurité pour payer en ligne',
];
