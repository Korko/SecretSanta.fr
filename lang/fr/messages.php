<?php

return [
    // Général
    'app_name' => 'Secret Santa',
    'welcome' => 'Bienvenue sur Secret Santa',
    'loading' => 'Chargement...',
    'error' => 'Une erreur s\'est produite',
    'success' => 'Opération réussie',
    'confirm' => 'Êtes-vous sûr ?',
    'cancel' => 'Annuler',
    'save' => 'Enregistrer',
    'delete' => 'Supprimer',
    'edit' => 'Modifier',
    'create' => 'Créer',
    'update' => 'Mettre à jour',
    'back' => 'Retour',
    'next' => 'Suivant',
    'previous' => 'Précédent',
    'yes' => 'Oui',
    'no' => 'Non',
    
    // Authentification
    'login' => 'Connexion',
    'logout' => 'Déconnexion',
    'register' => 'S\'inscrire',
    'email' => 'Email',
    'password' => 'Mot de passe',
    'password_confirmation' => 'Confirmer le mot de passe',
    'remember_me' => 'Se souvenir de moi',
    'forgot_password' => 'Mot de passe oublié ?',
    'reset_password' => 'Réinitialiser le mot de passe',
    
    // Gestion des tirages
    'draw' => [
        'title' => 'Titre du tirage',
        'description' => 'Description',
        'event_date' => 'Date de l\'événement',
        'budget' => 'Budget',
        'create' => 'Créer un tirage',
        'update' => 'Mettre à jour le tirage',
        'delete' => 'Supprimer le tirage',
        'launch' => 'Lancer le tirage',
        'status' => [
            'draft' => 'Brouillon',
            'ready' => 'Prêt',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'failed' => 'Échoué',
        ],
        'participants_count' => ':count participant(s)',
        'no_participants' => 'Aucun participant pour le moment',
        'add_participant' => 'Ajouter un participant',
        'remove_participant' => 'Retirer un participant',
        'exclusions' => 'Exclusions',
        'add_exclusion' => 'Ajouter une règle d\'exclusion',
    ],
    
    // Participants
    'participant' => [
        'name' => 'Nom',
        'email' => 'Email',
        'status' => 'Statut',
        'joined_at' => 'A rejoint le',
        'invite' => 'Inviter un participant',
        'remove' => 'Retirer le participant',
        'access_key' => 'Clé d\'accès',
        'view_assignment' => 'Voir l\'attribution',
        'your_target' => 'Vous êtes le Secret Santa de',
        'status_types' => [
            'pending' => 'En attente',
            'accepted' => 'Accepté',
            'rejected' => 'Refusé',
        ],
    ],
    
    // Messages
    'message' => [
        'send' => 'Envoyer un message',
        'reply' => 'Répondre',
        'anonymous' => 'Message anonyme',
        'from_santa' => 'De votre Secret Santa',
        'to_recipient' => 'À votre destinataire',
        'placeholder' => 'Tapez votre message...',
        'sent' => 'Message envoyé avec succès',
        'failed' => 'Échec de l\'envoi du message',
        'types' => [
            'question' => 'Question',
            'thank_you' => 'Remerciement',
            'hint' => 'Indice',
            'general' => 'Général',
        ],
    ],
    
    // Messages de validation
    'validation' => [
        'required' => 'Ce champ est obligatoire',
        'email' => 'Veuillez entrer une adresse email valide',
        'min' => 'Doit contenir au moins :min caractères',
        'max' => 'Ne doit pas dépasser :max caractères',
        'confirmed' => 'La confirmation ne correspond pas',
        'unique' => 'Cette valeur est déjà prise',
        'date' => 'Veuillez entrer une date valide',
        'after' => 'Doit être une date après le :date',
    ],
    
    // Messages d'erreur
    'errors' => [
        'not_found' => 'Ressource non trouvée',
        'unauthorized' => 'Vous n\'êtes pas autorisé à effectuer cette action',
        'forbidden' => 'Accès interdit',
        'server_error' => 'Une erreur interne du serveur s\'est produite',
        'draw_failed' => 'Impossible de terminer le tirage. Veuillez vérifier les règles d\'exclusion.',
        'invalid_key' => 'Clé d\'accès invalide',
        'expired_link' => 'Ce lien a expiré',
    ],
    
    // Messages de succès
    'success_messages' => [
        'draw_created' => 'Tirage créé avec succès',
        'draw_updated' => 'Tirage mis à jour avec succès',
        'draw_deleted' => 'Tirage supprimé avec succès',
        'draw_launched' => 'Tirage lancé avec succès',
        'participant_added' => 'Participant ajouté avec succès',
        'participant_removed' => 'Participant retiré avec succès',
        'message_sent' => 'Message envoyé avec succès',
        'profile_updated' => 'Profil mis à jour avec succès',
    ],
];