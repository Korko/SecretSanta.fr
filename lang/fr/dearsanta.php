<?php

declare(strict_types=1);

return [
    'reminder' => "Rappelez vous que votre Père/Mère Noël secrêt ne pourra pas vous répondre ! Pensez bien à lui donner le maximum d'informations possibles.",
    'report' => [
        'question' => 'Un problème ?',
        'identity' => 'Êtes-vous bien {name} ?',
        'not_me' => 'Non, cet email ne m\'est pas destiné',
        'its_me' => 'Oui, c\'est moi',
        'not_participating' => 'Je n\'ai jamais demandé à participer',
        'cancel' => 'Annuler le signalement',
        'back' => 'Retour',
        'sent' => [
            'not_me' => '{organizer} a été prévenu(e) par email que cet email ne vous était pas destiné, pour corriger l\'adresse.',
            'not_participating' => '{organizer} a été prévenu(e) par email que vous ne participez pas.',
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
    'withdraw' => [
        'button' => 'Je ne veux plus participer',
        'done' => 'Vous ne participez plus à cet évènement. Votre père/mère noël secret a reçu une nouvelle cible et l\'organisateur/organisatrice a été prévenu(e).',
        'confirm' => [
            'title' => 'Se désister de l\'évènement ?',
            'body' => 'Vous quitterez le tirage au sort : votre père/mère noël secret recevra une nouvelle cible, {organizer} sera prévenu(e) par email et cette page ne fonctionnera plus. Seul(e) {organizer} pourra vous réintégrer. Si cet email ne vous était pas destiné ou que vous n\'avez jamais demandé à participer, utilisez plutôt le signalement.',
            'help' => 'Saisir votre nom, « {name} », en dessous pour confirmer.',
            'ok' => 'Me désister',
            'cancel' => 'Annuler',
        ],
        'blocked' => 'Votre désistement donnerait à quelqu\'un une personne qu\'il ou elle a exclue. Demandez à :organizer de vous retirer depuis sa page d\'organisation.',
    ],
    'suggestion' => [
        'open' => 'Suggérer une idée cadeau pour quelqu\'un d\'autre',
        'help' => 'Votre idée sera envoyée par email au père/mère noël secret de la personne choisie, sans que vous sachiez qui c\'est.',
        'recipient' => 'Pour qui ?',
        'choose' => 'Choisir un(e) participant(e)',
        'content' => 'Votre idée',
        'placeholder' => 'Elle adore les jeux de société...',
        'signed' => 'Signer avec mon nom (sinon, l\'idée reste anonyme)',
        'signed_short' => 'signée',
        'send' => 'Envoyer l\'idée',
        'list' => 'Idées envoyées',
        'received' => [
            'title' => 'Idées cadeaux reçues pour votre cible',
            'anonymous' => 'Anonyme',
        ],
    ],
];
