<?php

declare(strict_types=1);

return [
    'reminder' => "Rappelez vous que votre Père/Mère Noël secrêt ne pourra pas vous répondre ! Pensez bien à lui donner le maximum d'informations possibles.",
    'report' => [
        'question' => 'Un problème ?',
        'not_me' => 'Cet email ne m\'est pas destiné',
        'not_participating' => 'Je ne participe pas',
        'cancel' => 'Annuler le signalement',
        'sent' => [
            'not_me' => '{organizer} verra sur sa page que cet email ne vous était pas destiné.',
            'not_participating' => '{organizer} verra sur sa page que vous ne participez pas.',
        ],
    ],
    'targets' => [
        'revealed' => 'L\'organisateur/organisatrice a consulté la liste des cibles',
        'secret' => 'La liste des cibles n\'a pas été consultée par l\'organisateur/organisatrice',
        'ended' => 'L\'évènement est terminé : la liste des cibles est maintenant visible par l\'organisateur/organisatrice',
    ],
    'list' => [
        'date' => 'Date d\'envoi',
        'body' => 'Corps du message',
        'status' => 'Status de réception de l\'email',
        'empty' => 'Aucun email envoyé pour le moment',
        'caption' => 'Liste des emails envoyés au/à la Père/Mère Noël',
    ],
    'content' => [
        'label' => 'Contenu du mail',
        'placeholder' => 'Cher Papa Noël...',
    ],
    'resend' => [
        'button' => 'Me ré-envoyer les mails que j\'ai reçu de ma cible',
    ],
    'email' => [
        'current' => 'Vos emails arrivent à :email.',
        'open' => 'Changer d\'adresse',
        'new' => 'Nouvelle adresse',
        'send_code' => 'Recevoir un code',
        'code' => 'Code reçu à :email :',
        'confirm' => 'Confirmer',
        'code_sent' => 'Un code vient d\'être envoyé à :email. Saisissez-le ici pour confirmer le changement.',
        'changed' => 'Vos emails arriveront désormais à :email. Le nom de votre cible vient d\'y être renvoyé.',
        'wrong_code' => 'Ce code n\'est pas le bon.',
        'expired' => 'Ce code n\'est plus valable, demandez-en un nouveau.',
        'same' => 'C\'est déjà votre adresse.',
    ],
];
