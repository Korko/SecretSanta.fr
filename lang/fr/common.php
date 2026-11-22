<?php

declare(strict_types=1);

return [
    'internal' => 'Une erreur est survenue',
    'success' => 'Succès',

    'copy_failed' => 'Copie impossible : copiez :text à la main',
    'copied' => 'Copié dans le presse papier',

    'fetcher' => [
        'load' => 'Charger',
        'loading' => 'Chargement en cours...',
    ],

    'form' => [
        'send' => 'Envoyer',
        'sending' => 'Envoi en cours',
        'sent' => 'Envoyé',
        'reset' => 'Commencer un nouveau tirage',
    ],

    'modal' => [
        'close' => 'Fermer',
    ],

    'email' => [
        'redo' => 'Ré-envoyer',
        'status' => [
            'created' => 'En attente d\'envoi',
            'sending' => 'Envoi en cours',
            'sent' => 'Envoyé',
            'error' => 'Erreur',
            'delayed' => 'Retardé, nouvel essai en cours',
            'received' => 'Reçu',
        ],
        'recent' => 'Vous ne pouvez pas relancer un même email trop rapidement',
        // Why it bounced, when the delivery report says (BounceReason)
        'bounce' => [
            'address' => 'Adresse inexistante : à corriger',
            'full' => 'Boîte de réception pleine : à renvoyer plus tard',
            'refused' => 'Refusé par le serveur du destinataire, l\'adresse n\'est pas en cause',
        ],
    ],

    'csv' => [
        'generated' => 'Fichier généré le :date sur :app (:url)',
        'usage' => 'Ce fichier peut être utilisé pour préremplir les participants ainsi que les exclusions associées',
    ],
];
