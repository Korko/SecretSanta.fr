<?php

return [
    'common' => [
        'regards' => 'Cordialement',
    ],

    'draw_completed' => [
        'subject' => 'Tirage Secret Santa Terminé - :title',
        'greeting' => 'Bonjour :name !',
        'body' => 'Le tirage Secret Santa ":title" a été effectué avec succès.',
        'participants_count' => 'Les :count participants ont tous reçu leur attribution.',
        'next_steps' => 'Prochaines étapes :',
        'step1' => 'Chaque participant recevra un email avec la personne qui lui a été attribuée',
        'step2' => 'Les participants peuvent accéder à leur lien sécurisé pour voir leur attribution',
        'step3' => 'La messagerie anonyme est maintenant activée entre les Secret Santa et leurs destinataires',
        'button' => 'Voir les détails du tirage',
        'footer' => 'Merci d\'organiser ce Secret Santa avec nous !',
    ],

    'participant_invitation' => [
        'subject' => 'Invitation au Secret Santa - :title',
        'greeting' => 'Bonjour :name !',
        'body' => ':organizer vous invite à participer au tirage Secret Santa ":title".',
        'event_details' => 'Détails de l\'événement :',
        'date' => 'Date de l\'événement : :date',
        'budget' => 'Budget suggéré : :budget',
        'instructions' => 'Pour rejoindre ce tirage Secret Santa, cliquez sur le bouton ci-dessous et entrez votre clé d\'accès sécurisée.',
        'access_key' => 'Votre clé d\'accès : :key',
        'button' => 'Rejoindre le Secret Santa',
        'footer' => 'Conservez votre clé d\'accès en sécurité - vous en aurez besoin pour accéder à votre attribution.',
    ],

    'participant_draw_ready' => [
        'subject' => 'Votre attribution Secret Santa - :title',
        'greeting' => 'Bonjour :name !',
        'body' => 'Le tirage pour ":title" est terminé et votre destinataire Secret Santa vous a été attribué !',
        'target_intro' => 'Vous serez le Secret Santa de :',
        'exclusions_notice' => 'Note : Ce tirage avait :count règle(s) d\'exclusion appliquée(s).',
        'button' => 'Voir les détails',
        'footer' => 'N\'oubliez pas de garder votre attribution secrète et amusez-vous !',
    ],

    'draw_failed' => [
        'subject' => 'Échec du tirage - :title',
        'greeting' => 'Bonjour :name,',
        'body' => 'Malheureusement, le tirage Secret Santa pour ":title" n\'a pas pu être complété.',
        'error_message' => 'Détails de l\'erreur :',
        'suggestion' => 'Veuillez vérifier vos règles d\'exclusion et réessayer. Vous devrez peut-être ajuster les exclusions pour rendre un tirage valide possible.',
        'button' => 'Gérer le tirage',
        'footer' => 'Si vous continuez à rencontrer des problèmes, veuillez contacter le support.',
    ],

    'registration_request' => [
        'subject' => 'Nouvelle demande d\'inscription - :title',
        'greeting' => 'Bonjour :name,',
        'body' => ':participant a demandé à rejoindre votre tirage Secret Santa ":title".',
        'participant_info' => 'Informations du participant :',
        'field' => 'Champ',
        'value' => 'Valeur',
        'name' => 'Nom',
        'email' => 'Email',
        'date' => 'Date de demande',
        'action_required' => 'Veuillez examiner et approuver ou rejeter cette demande d\'inscription.',
        'button' => 'Examiner la demande',
        'footer' => 'Les participants ne peuvent pas rejoindre le tirage tant que vous n\'avez pas approuvé leur inscription.',
    ],

    'registration_accepted' => [
        'subject' => 'Inscription approuvée - :title',
        'greeting' => 'Bonjour :name !',
        'body' => 'Bonne nouvelle ! Votre inscription au tirage Secret Santa ":title" a été approuvée par :organizer.',
        'next_steps' => 'Prochaines étapes :',
        'step1' => 'Attendez que le tirage soit effectué',
        'step2' => 'Vous recevrez un email avec votre attribution Secret Santa',
        'step3' => 'Utilisez votre lien sécurisé pour accéder aux idées cadeaux et envoyer des messages anonymes',
        'footer' => 'Préparez-vous pour le plaisir du Secret Santa !',
    ],

    'registration_rejected' => [
        'subject' => 'Mise à jour de l\'inscription - :title',
        'greeting' => 'Bonjour :name,',
        'body' => 'Votre demande d\'inscription au tirage Secret Santa ":title" a été examinée.',
        'reason_intro' => 'Informations supplémentaires :',
        'footer' => 'Si vous avez des questions, veuillez contacter l\'organisateur.',
    ],

    'message_notification' => [
        'subject' => 'Nouveau message - :title',
        'greeting' => 'Bonjour :name !',
        'body' => 'Vous avez reçu un nouveau message dans le tirage Secret Santa ":title".',
        'question_received' => 'Votre Secret Santa vous a posé une question !',
        'thank_you_received' => 'Vous avez reçu un message de remerciement !',
        'message_received' => 'Un nouveau message vous attend.',
        'button' => 'Voir le message',
        'footer' => 'Les messages sont anonymes pour préserver la surprise du Secret Santa !',
    ],

    'organizer_notification' => [
        'subject' => 'Mise à jour - :title',
        'greeting' => 'Bonjour :name,',
        'body' => 'Il y a une mise à jour pour votre tirage Secret Santa ":title".',
        'participant_joined' => 'Un nouveau participant a rejoint le tirage.',
        'message_sent' => 'Un message a été envoyé entre les participants.',
        'draw_completed' => 'Le tirage a été complété avec succès !',
        'general_update' => 'Il y a eu une activité dans votre tirage.',
        'button' => 'Voir les détails',
        'footer' => 'Restez informé de votre événement Secret Santa.',
    ],

    'admin_alert' => [
        'subject' => 'Alerte système [:level]',
        'greeting' => 'Alerte Administrateur Système',
        'level' => 'Niveau d\'alerte : :level',
        'context' => 'Contexte supplémentaire :',
        'key' => 'Clé',
        'value' => 'Valeur',
        'timestamp' => 'Horodatage :',
    ],
];