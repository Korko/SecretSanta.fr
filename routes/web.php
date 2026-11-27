<?php

declare(strict_types=1);

use App\Http\Controllers\BroadcastAuthController;
use App\Http\Controllers\DearSantaController;
use App\Http\Controllers\FixOrganizerController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\RandomFormController;
use App\Models\Draw;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [RandomFormController::class, 'view']);
Route::post('/', [RandomFormController::class, 'handle'])->middleware('throttle:draw');

Route::get('/faq', [RandomFormController::class, 'faq'])->name('faq');
Route::view('/legal', 'legal')->name('legal');

Route::pattern('draw', '[0-9a-zA-Z]{'.config()->integer('hashids.connections.draw.length').',}');
Route::pattern('participant', '[0-9a-zA-Z]{'.config()->integer('hashids.connections.santa.length').',}');
Route::pattern('dearSanta', '[0-9a-zA-Z]{'.config()->integer('hashids.connections.dearSanta.length').',}');

Route::middleware('signed')->group(function (): void {
    Route::get('/dearSanta/{participant}', [DearSantaController::class, 'view'])
        ->name('dearSanta')
        ->missing(fn () => response()->view('missingDraw', [], 404));

    // Private channels: the signed URL stands in for an account
    Route::post('/dearSanta/{participant}/broadcasting/auth', [BroadcastAuthController::class, 'participant'])
        ->name('dearSanta.broadcastAuth');
    Route::post('/draw/{draw}/broadcasting/auth', [BroadcastAuthController::class, 'draw'])
        ->name('organizerPanel.broadcastAuth');

    Route::middleware('decrypt.secret:participant,name')->group(function (): void {
        Route::get('/participant/{participant}', [DearSantaController::class, 'fetch'])
            ->name('dearSanta.fetch');
        Route::post('/dearSanta/{participant}', [DearSantaController::class, 'handle'])
            ->name('dearSanta.contact')->middleware('throttle:email');
        // Emails the organizer: limited like the other emails
        Route::post('/dearSanta/{participant}/report', [DearSantaController::class, 'report'])
            ->name('dearSanta.report')->middleware('throttle:email');
        Route::get('/dearSanta/{participant}/fetchState', [DearSantaController::class, 'fetchState'])
            ->name('dearSanta.fetchState');
        Route::post('/dearSanta/{participant}/resendTarget', [DearSantaController::class, 'resendTarget'])
            ->name('dearSanta.resend_target')->middleware('throttle:email');
        // A code sent to any address, then the target email again: limited like the other emails
        Route::post('/dearSanta/{participant}/email', [DearSantaController::class, 'requestEmailChange'])
            ->name('dearSanta.request_email_change')->middleware('throttle:email');
        Route::post('/dearSanta/{participant}/email/confirm', [DearSantaController::class, 'changeEmail'])
            ->name('dearSanta.change_email')->middleware('throttle:email');
        Route::post('/dearSanta/{participant}/withdraw', [DearSantaController::class, 'withdraw'])
            ->name('dearSanta.withdraw')->middleware('throttle:email');
        Route::post('/dearSanta/{participant}/suggest', [DearSantaController::class, 'suggest'])
            ->name('dearSanta.suggest')->middleware('throttle:email');
    });

    Route::get('/org/{draw}', [OrganizerController::class, 'view'])->name('organizerPanel')
        ->missing(fn () => response()->view('missingDraw', [], 404));

    Route::middleware('decrypt.secret:draw,mail_title')->group(function (): void {
        Route::get('/draw/{draw}', [OrganizerController::class, 'fetch'])
            ->name('organizerPanel.fetch');
        Route::delete('/draw/{draw}', [OrganizerController::class, 'delete'])
            ->name('organizerPanel.delete');
        Route::get('/draw/{draw}/csvInit', [OrganizerController::class, 'csvInit'])
            ->name('organizerPanel.csvInit');
        Route::get('/draw/{draw}/csvFinal', [OrganizerController::class, 'csvFinal'])
            ->name('organizerPanel.csvFinal');
        Route::post('/draw/{draw}/revealTargets', [OrganizerController::class, 'revealTargets'])
            ->name('organizerPanel.revealTargets');
        Route::post('/draw/{draw}/participants', [OrganizerController::class, 'changeParticipants'])
            ->name('organizerPanel.changeParticipants')->middleware('throttle:email');
        Route::post('/draw/{draw}/restore', [OrganizerController::class, 'restore'])
            ->name('organizerPanel.restore')->middleware('throttle:email');
        Route::post('/draw/{draw}/changeDate', [OrganizerController::class, 'changeDate'])
            ->name('organizerPanel.changeDate');
        Route::post('/draw/{draw}/changeEventDetails', [OrganizerController::class, 'changeEventDetails'])
            ->name('organizerPanel.changeEventDetails');
        Route::get('/org/{draw}/fetchState', [OrganizerController::class, 'fetchState'])
            ->name('organizerPanel.fetchState');
    });
    // Participants of an ended draw can't be bound: say it is over rather than not found
    Route::middleware('decrypt.secret:participant,name')->missing(function (): never {
        $draw = request()->route('draw');
        if ($draw instanceof Draw) {
            $draw->abortIfExpired();
        }

        abort(404);
    })->group(function (): void {
        Route::post('/org/{draw}/{participant}/changeEmail', [OrganizerController::class, 'changeEmail'])
            ->name('organizerPanel.changeEmail')->middleware('throttle:email');
        Route::post('/org/{draw}/{participant}/changeName', [OrganizerController::class, 'changeName'])
            ->name('organizerPanel.changeName')->middleware('throttle:email');
    });
});

Route::get('/fix', [FixOrganizerController::class, 'view'])->name('fixOrganizer');
Route::post('/fix', [FixOrganizerController::class, 'handle'])->name('fixOrganizer.handle')->middleware('throttle:email');

// Route::get( '/{status}', fn( int | null $status = null ) => Illuminate\Support\Facades\App::abort( $status ) )->where('status', '[0-9]{3}');
