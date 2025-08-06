<?php
<?php

use App\Http\Controllers\Draw\AddParticipantController;

// Controllers for draws
use App\Http\Controllers\Draw\CreateDrawController;
use App\Http\Controllers\Draw\GetDrawDetailsController;
use App\Http\Controllers\Draw\LaunchDrawController;
use App\Http\Controllers\Draw\RegenerateParticipantLinkController;
use App\Http\Controllers\Draw\RevealDrawController;
use App\Http\Controllers\Draw\ReviewParticipantController;
use App\Http\Controllers\Draw\ToggleRegistrationController;
use App\Http\Controllers\Exclusion\AddParticipantsToGroupController;

// Controllers for exclusions
use App\Http\Controllers\Exclusion\CreateBulkExclusionsController;
use App\Http\Controllers\Exclusion\CreateExclusionController;
use App\Http\Controllers\Exclusion\CreateExclusionGroupController;
use App\Http\Controllers\Exclusion\DeleteExclusionController;
use App\Http\Controllers\Exclusion\DeleteExclusionGroupController;
use App\Http\Controllers\Exclusion\GetDrawExclusionsController;
use App\Http\Controllers\Exclusion\RemoveParticipantFromGroupController;
use App\Http\Controllers\Exclusion\ValidateExclusionConstraintsController;
use App\Http\Controllers\Message\AddReactionController;

// Controllers for messages
use App\Http\Controllers\Message\GetConversationController;
use App\Http\Controllers\Message\GetParticipantMessagesController;
use App\Http\Controllers\Message\ManagePredefinedResponsesController;
use App\Http\Controllers\Message\ModerateMessageController;
use App\Http\Controllers\Message\RemoveReactionController;
use App\Http\Controllers\Message\ReportMessageController;
use App\Http\Controllers\Message\SendMessageController;
use App\Http\Controllers\Message\SendPredefinedResponseController;
use App\Http\Controllers\Participant\AuthenticateParticipantController;

// Controllers for participants
use App\Http\Controllers\Participant\GetParticipantDetailsController;
use App\Http\Controllers\Participant\RegisterForDrawController;
use App\Http\Controllers\User\CreateUserProfileController;

// Controllers for users
use App\Http\Controllers\User\DeleteUserProfileController;
use App\Http\Controllers\User\GetUserProfilesController;
use App\Http\Controllers\User\LoginUserController;
use App\Http\Controllers\User\LogoutUserController;
use App\Http\Controllers\User\RegisterUserController;
use App\Http\Controllers\User\UpdateUserProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api/v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Draw management routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('draws')->group(function () {
        // Create a new draw
        Route::post('/', CreateDrawController::class);

        // Get draw details (requires master key)
        Route::get('/{draw:uuid}', GetDrawDetailsController::class);

        // Participant management
        Route::post('/{draw:uuid}/participants', AddParticipantController::class);
        Route::patch('/{draw:uuid}/participants/{participant:uuid}/review', ReviewParticipantController::class);
        Route::post('/{draw:uuid}/participants/{participant:uuid}/regenerate-link', RegenerateParticipantLinkController::class);

        // Registration management
        Route::patch('/{draw:uuid}/registration', ToggleRegistrationController::class);

        // Launch the draw
        Route::post('/{draw:uuid}/launch', LaunchDrawController::class);

        // Reveal results
        Route::post('/{draw:uuid}/reveal', RevealDrawController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Exclusion management routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('draws/{draw:uuid}/exclusions')->group(function () {
        // Get all exclusions
        Route::get('/', GetDrawExclusionsController::class);

        // Create an individual exclusion
        Route::post('/', CreateExclusionController::class);

        // Create bulk exclusions
        Route::post('/bulk', CreateBulkExclusionsController::class);

        // Delete an exclusion
        Route::delete('/{exclusion}', DeleteExclusionController::class);

        // Validate constraints
        Route::get('/validate', ValidateExclusionConstraintsController::class);
    });

    // Exclusion groups
    Route::prefix('draws/{draw:uuid}/exclusion-groups')->group(function () {
        // Create a group
        Route::post('/', CreateExclusionGroupController::class);
    });

    Route::prefix('exclusion-groups/{group}')->group(function () {
        // Add participants to group
        Route::post('/participants', AddParticipantsToGroupController::class);

        // Remove a participant from group
        Route::delete('/participants/{participant:uuid}', RemoveParticipantFromGroupController::class);

        // Delete the group
        Route::delete('/', DeleteExclusionGroupController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Participant routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('participants')->group(function () {
        // Authenticate a participant with their individual key
        Route::post('/{participant:uuid}/authenticate', AuthenticateParticipantController::class);

        // Get participant details
        Route::get('/{participant:uuid}', GetParticipantDetailsController::class)
            ->middleware('participant.auth');

        // Register for an open draw
        Route::post('/register/{draw:uuid}', RegisterForDrawController::class);

        // Participant messages
        Route::prefix('{participant:uuid}/messages')->middleware('participant.auth')->group(function () {
            // Get messages
            Route::get('/', GetParticipantMessagesController::class);

            // Send a message
            Route::post('/', SendMessageController::class);

            // Send a predefined response
            Route::post('/predefined', SendPredefinedResponseController::class);
        });

        // Conversation with another participant
        Route::get('{participant:uuid}/conversation/{other:uuid}', GetConversationController::class)
            ->middleware('participant.auth');
    });

    /*
    |--------------------------------------------------------------------------
    | Message routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('messages')->middleware('participant.auth')->group(function () {
        // Add a reaction
        Route::post('/{message}/reactions', AddReactionController::class);

        // Remove a reaction
        Route::delete('/{message}/reactions', RemoveReactionController::class);

        // Report a message
        Route::post('/{message}/report', ReportMessageController::class);

        // Moderate a message (organizer only)
        Route::patch('/{message}/moderate', ModerateMessageController::class)
            ->middleware('organizer');
    });

    /*
    |--------------------------------------------------------------------------
    | Predefined responses routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('draws/{draw:uuid}/predefined-responses')->middleware('participant.auth')->group(function () {
        // Manage predefined responses (organizer only)
        Route::put('/', ManagePredefinedResponsesController::class)
            ->middleware('organizer');
    });

    /*
    |--------------------------------------------------------------------------
    | User authentication routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->group(function () {
        // Register
        Route::post('/register', RegisterUserController::class);

        // Login
        Route::post('/login', LoginUserController::class);

        // Logout
        Route::post('/logout', LogoutUserController::class)
            ->middleware('auth:sanctum');
    });

    /*
    |--------------------------------------------------------------------------
    | User profile routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('user')->middleware('auth:sanctum')->group(function () {
        // Get profiles
        Route::get('/profiles', GetUserProfilesController::class);

        // Create a profile
        Route::post('/profiles', CreateUserProfileController::class);

        // Update a profile
        Route::put('/profiles/{profile}', UpdateUserProfileController::class);

        // Delete a profile
        Route::delete('/profiles/{profile}', DeleteUserProfileController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
*/

Route::prefix('webhooks')->group(function () {
    // Webhook for failed jobs
    Route::post('/failed-jobs', function () {
        // Logic to handle failed jobs
        // TODO
    });
});
