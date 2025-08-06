<?php

use Illuminate\Support\Facades\Route;

// Controllers pour les tirages
use App\Http\Controllers\Draw\CreateDrawController;
use App\Http\Controllers\Draw\AddParticipantController;
use App\Http\Controllers\Draw\ReviewParticipantController;
use App\Http\Controllers\Draw\ToggleRegistrationController;
use App\Http\Controllers\Draw\LaunchDrawController;
use App\Http\Controllers\Draw\RevealDrawController;
use App\Http\Controllers\Draw\RegenerateParticipantLinkController;
use App\Http\Controllers\Draw\GetDrawDetailsController;

// Controllers pour les exclusions
use App\Http\Controllers\Exclusion\CreateExclusionController;
use App\Http\Controllers\Exclusion\CreateBulkExclusionsController;
use App\Http\Controllers\Exclusion\DeleteExclusionController;
use App\Http\Controllers\Exclusion\CreateExclusionGroupController;
use App\Http\Controllers\Exclusion\AddParticipantsToGroupController;
use App\Http\Controllers\Exclusion\RemoveParticipantFromGroupController;
use App\Http\Controllers\Exclusion\DeleteExclusionGroupController;
use App\Http\Controllers\Exclusion\GetDrawExclusionsController;
use App\Http\Controllers\Exclusion\ValidateExclusionConstraintsController;

// Controllers pour les messages
use App\Http\Controllers\Message\SendMessageController;
use App\Http\Controllers\Message\SendPredefinedResponseController;
use App\Http\Controllers\Message\GetParticipantMessagesController;
use App\Http\Controllers\Message\GetConversationController;
use App\Http\Controllers\Message\AddReactionController;
use App\Http\Controllers\Message\RemoveReactionController;
use App\Http\Controllers\Message\ReportMessageController;
use App\Http\Controllers\Message\ModerateMessageController;
use App\Http\Controllers\Message\ManagePredefinedResponsesController;

// Controllers pour les participants
use App\Http\Controllers\Participant\AuthenticateParticipantController;
use App\Http\Controllers\Participant\GetParticipantDetailsController;
use App\Http\Controllers\Participant\RegisterForDrawController;

// Controllers pour les utilisateurs
use App\Http\Controllers\User\RegisterUserController;
use App\Http\Controllers\User\LoginUserController;
use App\Http\Controllers\User\LogoutUserController;
use App\Http\Controllers\User\GetUserProfilesController;
use App\Http\Controllers\User\CreateUserProfileController;
use App\Http\Controllers\User\UpdateUserProfileController;
use App\Http\Controllers\User\DeleteUserProfileController;

/*
|--------------------------------------------------------------------------
| Routes API publiques
|--------------------------------------------------------------------------
*/

Route::prefix('api/v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Routes de gestion des tirages
    |--------------------------------------------------------------------------
    */

    Route::prefix('draws')->group(function () {
        // Créer un nouveau tirage
        Route::post('/', CreateDrawController::class);

        // Détails d'un tirage (nécessite master key)
        Route::get('/{draw:uuid}', GetDrawDetailsController::class);

        // Gestion des participants
        Route::post('/{draw:uuid}/participants', AddParticipantController::class);
        Route::patch('/{draw:uuid}/participants/{participant:uuid}/review', ReviewParticipantController::class);
        Route::post('/{draw:uuid}/participants/{participant:uuid}/regenerate-link', RegenerateParticipantLinkController::class);

        // Gestion des inscriptions
        Route::patch('/{draw:uuid}/registration', ToggleRegistrationController::class);

        // Lancer le tirage
        Route::post('/{draw:uuid}/launch', LaunchDrawController::class);

        // Révéler les résultats
        Route::post('/{draw:uuid}/reveal', RevealDrawController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Routes de gestion des exclusions
    |--------------------------------------------------------------------------
    */

    Route::prefix('draws/{draw:uuid}/exclusions')->group(function () {
        // Récupérer toutes les exclusions
        Route::get('/', GetDrawExclusionsController::class);

        // Créer une exclusion individuelle
        Route::post('/', CreateExclusionController::class);

        // Créer des exclusions en lot
        Route::post('/bulk', CreateBulkExclusionsController::class);

        // Supprimer une exclusion
        Route::delete('/{exclusion}', DeleteExclusionController::class);

        // Valider les contraintes
        Route::get('/validate', ValidateExclusionConstraintsController::class);
    });

    // Groupes d'exclusion
    Route::prefix('draws/{draw:uuid}/exclusion-groups')->group(function () {
        // Créer un groupe
        Route::post('/', CreateExclusionGroupController::class);
    });

    Route::prefix('exclusion-groups/{group}')->group(function () {
        // Ajouter des participants au groupe
        Route::post('/participants', AddParticipantsToGroupController::class);

        // Retirer un participant du groupe
        Route::delete('/participants/{participant:uuid}', RemoveParticipantFromGroupController::class);

        // Supprimer le groupe
        Route::delete('/', DeleteExclusionGroupController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Routes pour les participants
    |--------------------------------------------------------------------------
    */

    Route::prefix('participants')->group(function () {
        // Authentifier un participant avec sa clé individuelle
        Route::post('/{participant:uuid}/authenticate', AuthenticateParticipantController::class);

        // Détails d'un participant
        Route::get('/{participant:uuid}', GetParticipantDetailsController::class)
            ->middleware('participant.auth');

        // S'inscrire à un tirage ouvert
        Route::post('/register/{draw:uuid}', RegisterForDrawController::class);

        // Messages du participant
        Route::prefix('{participant:uuid}/messages')->middleware('participant.auth')->group(function () {
            // Récupérer les messages
            Route::get('/', GetParticipantMessagesController::class);

            // Envoyer un message
            Route::post('/', SendMessageController::class);

            // Envoyer une réponse prédéfinie
            Route::post('/predefined', SendPredefinedResponseController::class);
        });

        // Conversation avec un autre participant
        Route::get('{participant:uuid}/conversation/{other:uuid}', GetConversationController::class)
            ->middleware('participant.auth');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes pour les messages
    |--------------------------------------------------------------------------
    */

    Route::prefix('messages')->middleware('participant.auth')->group(function () {
        // Ajouter une réaction
        Route::post('/{message}/reactions', AddReactionController::class);

        // Supprimer une réaction
        Route::delete('/{message}/reactions', RemoveReactionController::class);

        // Signaler un message
        Route::post('/{message}/report', ReportMessageController::class);

        // Modérer un message (organisateur uniquement)
        Route::patch('/{message}/moderate', ModerateMessageController::class)
            ->middleware('organizer');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes pour les réponses prédéfinies
    |--------------------------------------------------------------------------
    */

    Route::prefix('draws/{draw:uuid}/predefined-responses')->middleware('participant.auth')->group(function () {
        // Gérer les réponses prédéfinies (organisateur uniquement)
        Route::put('/', ManagePredefinedResponsesController::class)
            ->middleware('organizer');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes d'authentification utilisateur
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->group(function () {
        // Inscription
        Route::post('/register', RegisterUserController::class);

        // Connexion
        Route::post('/login', LoginUserController::class);

        // Déconnexion
        Route::post('/logout', LogoutUserController::class)
            ->middleware('auth:sanctum');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes pour les profils utilisateur
    |--------------------------------------------------------------------------
    */

    Route::prefix('user')->middleware('auth:sanctum')->group(function () {
        // Récupérer les profils
        Route::get('/profiles', GetUserProfilesController::class);

        // Créer un profil
        Route::post('/profiles', CreateUserProfileController::class);

        // Mettre à jour un profil
        Route::put('/profiles/{profile}', UpdateUserProfileController::class);

        // Supprimer un profil
        Route::delete('/profiles/{profile}', DeleteUserProfileController::class);
    });
});
