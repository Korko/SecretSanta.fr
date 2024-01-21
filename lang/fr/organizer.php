<?php

return [
    'list' => [
        'name' => 'Nom',
        'email' => 'Adresse Email',
        'status' => "Status d'envoi de l'email",
        'target' => 'Nom de la cible',
        'caption' => 'Liste des participant(e)s',
        'withdraw' => 'Retirer',
    ],
    'changed' => 'Modifié avec succès !',
    'withdrawn' => ":name ne participe plus à l'évènement.",
    'deleted' => 'Toutes les données ont été supprimées',
    'download' => [
        'button' => 'Télécharger le récapitulatif',
        'button-tooltip' => [
            'title' => 'Récapitulatif',
            'content' => "Ce sont les données telles que vous les avez remplies à la génération de l'évènement. Seules les adresses e-mail peuvent avoir changé, pour refléter les modifications que vous avez pu faire ici.",
        ],
        'button_initial' => 'Télécharger le récapitulatif initial',
        'button_initial-tooltip' => [
            'title' => 'Récapitulatif initial',
            'content' => "Ce sont les données telles que vous les avez remplies à la génération de l'évènement. Seules les adresses e-mail peuvent avoir changé, pour refléter les modifications que vous avez pu faire ici.",
        ],
        'button_final' => 'Télécharger le récapitulatif complété',
        'button_final-tooltip' => [
            'title' => 'Récapitulatif complété',
            'explain' => "Les données sont les mêmes que dans le récapitulatif initial mais ont été ajoutées aux exclusions de chaque participant(e) la cible qu'il a eu durant cet évènement. A moins que ceci amène à un blocage où on ne puisse plus trouver de cible à chaque participant(e) pour la prochaine fois.",
        ],
    ],
    'end' => [
        'button' => "Terminer l'évènement",
        'button-tooltip' => [
            'title' => "Terminer l'évènement",
            'content' => "Une fois l'évènement terminé, les cibles de chaque participant sera affiché et vous pourrez aussi télécharger le récapitulatif complété. Par ailleurs les interfaces pour communiquer avec son père noël secret seront désactivées et les données du tirage au sort seront supprimées dans 7 jours. Cette action est définitive.",
        ],
    ],
    'purge' => [
        'button' => 'Supprimer tout',
        'button-tooltip' => [
            'title' => 'Supprimer toutes les données',
            'content' => 'Sans action de votre part, toutes les données seront automatiquement supprimées 3 mois après le dernier email échangé. Cette action supprime immédiatement la totalité des données de votre tirage au sort (noms des participants, adresses emails, messages échangés etc). Cette action est définitive.',
        ],
        'confirm' => [
            'title' => 'Êtes-vous sûr de vouloir supprimer la totalité des données avant le nettoyage automatique le :deletes_at ?',
            // Final recap available and draw not finished yet
            'body_final' => 'Vous ne pourrez plus télécharger le récapitulatif des tirages de cet évènement et les participant(e)s ne pourront plus écrire à leur père/mère noël secret. Cette action ne peut être annulée.',
            // Draw finished, final recap may be available or not
            'body_finished' => 'Vous ne pourrez plus télécharger le récapitulatif de cet évènement. Cette action ne peut être annulée.',
            // No final recap available and draw not finished yet
            'body_nofinal' => 'Vous ne pourrez plus télécharger le récapitulatif de cet évènement et les participant(e)s ne pourront plus écrire à leur père/mère noël secret. Cette action ne peut être annulée.',
            'value' => 'Supprimer toutes les données',
            // Use !: not to be transformed by vue-i18n-generator
            'help' => 'Saisir "[+!:verification]" en dessous pour confirmer.',
            'ok' => 'Ok',
            'cancel' => 'Annuler',
        ],
    ],
    'withdraw' => [
        'button' => 'Retirer',
        'confirm' => [
            'title' => "Êtes-vous sûr de vouloir retirer {name} de l'évènement ?",
            'body' => 'Tous les messages reçu de sa cible seront transmis à son nouveau père/mère noël secret. Cette action ne peut être annulée.',
            'value' => 'Annuler la participation',
            // Use !: not to be transformed by vue-i18n-generator
            'help' => 'Saisir "[+!:verification]" en dessous pour confirmer.',
            'ok' => 'Ok',
            'cancel' => 'Annuler',
        ],
    ],
    'finished' => 'Votre évènement a été marqué comme terminé ({finished_at}). Certaines actions ne sont plus disponibles, comme réenvoyer le nom de la cible à un(e) participant(e).',
];
