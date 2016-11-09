<?php

return [
    'nav.what' => "Qu'est-ce que c'est ?",
    'nav.how'  => "Comment faire ?",
    'nav.go'   => "Allez, c'est parti !",

    'subtitle'   => "Offrez-vous des cadeaux... secrètement !",

    'section.what.title' => "Qu'est-ce que c'est ?",
    'section.what.subtitle' => "Description du Secret Santa",
    'section.what.heading1' => "Le principe",
    'section.what.content1' => "Secret Santa est un moyen drôle et original de s'offrir anonymement des cadeaux entre amis, collègues...
Le déroulement est simple : chaque participant reçoit, de façon aléatoire, le nom de la personne à qui il devra faire un cadeau.
Le montant du cadeau est généralement fixé au préalable (2€, 5€, 10€...)
Le but n'est pas forcément de faire un beau cadeau mais d'être créatif !",

    'section.how.title' => "Comment faire ?",
    'section.how.subtitle' => "Vous allez voir, c'est simple !",
    'section.how.heading1' => "Première étape : spécifier le nombre et les noms des participants",
    'section.how.content1' => "Grâce aux boutons \"Ajouter un participant\" et \"Enlever un participant\", il est possible d'ajuster le nombre de personnes.
Pour chaque personne, indiquez un nom/prénom ou un pseudonyme. Deux participants ne peuvent avoir le même nom, sinon il est impossible de les différencier.
A noter que secretsanta.fr est conçu de façon à ce qu'une personne ne puisse pas se piocher elle-même.",
    'section.how.heading2' => "Deuxième étape : remplir les informations de contact et les exclusions",
    'section.how.content2' => "Vous avez la possibilité de choisir si les participants recevront le nom de leur cible par e-mail, par sms, ou les deux.
Pour cela, précisez pour chacun une adresse e-mail et/ou un numéro de téléphone portable.
(Optionel) Ajoutez des exclusions. Si vous ne voulez pas que les conjoints puissent se piocher l'un l'autre, remplissez le champ \"Partenaire\".",
    'section.how.heading3' => "Troisième étape : préparer le mail ou le sms",
    'section.how.content3' => "Il ne vous reste plus qu'à remplir le titre et le corps du courriel ou du SMS que les participants recevront.
Le mot clef \"{TARGET}\" est obligatoire dans le corps du message afin de donner à chaque personne sa \"cible\".
(Optionel) Vous pouvez aussi utiliser le mot clef \"{SANTA}\" qui sera remplacé par le nom du destinataire du message.",
    'section.how.notice' => "Pour votre information : secretsanta.fr ne sauvegarde aucune de vos données ni ne les utilise à d'autres fins. Le code source est disponible sur :link",

    'section.go.title' => "À vous de jouer !",
    'section.go.subtitle' => "Remplissez, cliquez et c'est parti !",

    'success' => "Envoyé avec succès !",

    'participants' => "Détails des participants",

    'participant.name' => "Nom ou pseudonyme",
    'participant.email' => "Adresse e-mail",
    'participant.phone' => "Téléphone",
    'participant.partner' => "Partenaire",

    'name.placeholder' => "exemple : Paul ou Korko",
    'email.placeholder' => "exemple : michel@aol.com",
    'phone.placeholder' => "ex : 0612345678",

    'partner.none' => "Aucun",
    'partner.remove' => "Enlever",
    'partner.add' => "Ajouter un participant",

    'mail.title' => "Titre du mail",
    'mail.title.placeholder' => "ex : Soirée secretsanta du 23 décembre chez Martin",

    'mail.content' => "Contenu du mail",
    'mail.content.placeholder' => "ex : Salut {SANTA}, pour la soirée secret santa, ta cible c'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !",
    'mail.content.tip1' => "Utilisez \"{SANTA}\" pour le nom de celui qui recevra le mail et \"{TARGET}\" pour le nom de sa cible.",
    'mail.content.tip2' => "Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau.",

    'sms.content' => "Contenu du sms <span class=\"tip\">(130 caractères max)</span>",
    'sms.content.placeholder' => "ex : Salut {SANTA}, pour la soirée secret santa du 23 décembre chez Martin, ta cible c'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !",
    'sms.content.tip1' => "Utilisez \"{SANTA}\" pour le nom de celui qui recevra le sms et \"{TARGET}\" pour le nom de sa cible.",
    'sms.content.tip2' => "Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau.",

    'submit' => "Lancez l'aléatoire !",
];
