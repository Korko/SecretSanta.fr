<?php

return [
    'list' => [
        'name'    => 'Nom',
        'email'   => 'Adresse Email',
        'status'  => 'Status d\'envoi de l\'email',
        'caption' => 'Liste des participants',
    ],
    'up_and_sent'   => 'Modifié avec succès !',
    'deleted'       => 'Toutes les données ont été supprimées',
    'download'      => [
        'button'  => 'Télécharger le récapitulatif',
        'button-tooltip' => '<h3>Récapitulatif</h3><p>Ce sont les données telles que vous les avez remplies à la génération de l\'évènement. Seules les adresses e-mail peuvent avoir changé, pour refléter les modifications que vous avez pu faire ici.</p>',
        'button_initial'  => 'Télécharger le récapitulatif initial',
        'button_initial-tooltip' => '<h3>Récapitulatif initial</h3><p>Ce sont les données telles que vous les avez remplies à la génération de l\'évènement. Seules les adresses e-mail peuvent avoir changé, pour refléter les modifications que vous avez pu faire ici.</p>',
        'button_final' => 'Télécharger le récapitulatif complété',
        'button_final-tooltip' => '<h3>Récapitulatif complété</h3><p>Les données sont les mêmes que dans le récapitulatif initial mais ont été ajoutées aux exclusions de charque participant la cible qu\'il a eu durant cet évènement. A moins que ceci amène à un blocage où on ne puisse plus trouver de cible à chaque participant pour la prochaine fois.</p><p class="border border-white border-1 rounded pl-2 pr-2 font-italic">Compte tenu de la date de l\'évènement définie, cette fonctionnalité n\'est disponible que du {expires_at} au {deleted_at}.</p>',
    ],
    'purge'         => [
        'button'  => 'Supprimer tout',
        'confirm' => [
            'title'  => 'Êtes-vous sûr de vouloir supprimer la totalité des données avant le nettoyage automatique le :deletion ?',
            // Final recap available and draw not expired yet
            'body_final'   => 'Vous ne pourrez plus télécharger le récapitulatif des tirages de cet évènement et les participants ne pourront plus écrire à leur père noël secret. Cette action ne peut être annulée.',
            // Draw expired, final recap may be available or not
            'body_expired' => 'Vous ne pourrez plus télécharger le récapitulatif de cet évènement. Cette action ne peut être annulée.',
            // No final recap available and draw not expired yet
            'body_nofinal' => 'Vous ne pourrez plus télécharger le récapitulatif de cet évènement et les participants ne pourront plus écrire à leur père noël secret. Cette action ne peut être annulée.',
            'value'  => 'Supprimer toutes les données',
            // Use !: not to be transformed by vue-i18n-generator
            'help'   => 'Saisir "[+!:verification]" en dessous pour confirmer.',
            'ok'     => 'Ok',
            'cancel' => 'Annuler',
        ],
    ],
    'suggest_redraw' => [
        'button' => 'Proposer un nouveau tirage',
        'confirm' => [
            'title'  => 'Vous vous apprétez à proposer aux participants de changer de cible',
            'body' => 'Chaque participant (vous y-compris) recevra un email lui demandant si il veut relancer l\'aléatoire et potentiellement piocher le nom de quelqu\'un d\'autre. Une fois qu\'il existe au moins une autre possibilité de cibles (en fonction des exclusions définies au début), vous pourrez clicker sur le bouton "Lancer le nouveau tirage" ici même. En attendant, il sera inactif.',
            'ok'  => 'Envoyer la proposition',
            'cancel' => 'Annuler',
        ],
        'message' => 'Les participants ont été prévenu de la proposition',
    ],
    'redraw' => [
        'button' => 'Lancer le nouveau tirage',
        'confirm' => [
        ],
        'disabled' => 'Trop peu de participants ont accepté le nouveau tirage pour avoir de nouvelles cibles.',
        'message' => 'Les participants volontaires ont correctement reçu une nouvelle cible.'
    ],
    'expired' => 'Votre évènement est passé ({expires_at}). Certaines actions ne sont plus disponibles, comme réenvoyer le nom de la cible à un participant.'
];
