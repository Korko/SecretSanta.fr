<?php

declare(strict_types=1);

use App\Http\Controllers\DearSantaController;
use App\Http\Controllers\FixOrganizerController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\RandomFormController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
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

RateLimiter::for('global', fn (Request $request) => Limit::perMinute(100)->by($request->ip())->response(fn () => abort(429)));

Route::get('/', [RandomFormController::class, 'view']);
Route::post('/', [RandomFormController::class, 'handle']);

Route::get('/faq', [RandomFormController::class, 'faq'])->name('faq');
Route::view('/legal', 'legal')->name('legal');

Route::pattern('draw', '[0-9a-zA-Z]{'.config()->integer('hashids.connections.draw.length').',}');
Route::pattern('participant', '[0-9a-zA-Z]{'.config()->integer('hashids.connections.santa.length').',}');
Route::pattern('dearSanta', '[0-9a-zA-Z]{'.config()->integer('hashids.connections.dearSanta.length').',}');

Route::middleware('signed')->group(function (): void {
    Route::get('/dearSanta/{participant}', [DearSantaController::class, 'view'])
        ->name('dearSanta')
        ->missing(fn () => response()->view('missingDraw', [], 404));

    Route::middleware('decrypt.iv:participant,name')->group(function (): void {
        Route::get('/participant/{participant}', [DearSantaController::class, 'fetch'])
            ->name('dearSanta.fetch');
        Route::post('/dearSanta/{participant}', [DearSantaController::class, 'handle'])
            ->name('dearSanta.contact');
        Route::post('/dearSanta/{participant}/report', [DearSantaController::class, 'report'])
            ->name('dearSanta.report');
        Route::get('/dearSanta/{participant}/fetchState', [DearSantaController::class, 'fetchState'])
            ->name('dearSanta.fetchState');
        Route::post('/dearSanta/{participant}/{dearSanta}/resend', [DearSantaController::class, 'resend'])
            ->name('dearSanta.resend');
        Route::post('/dearSanta/{participant}/resendTarget', [DearSantaController::class, 'resendTarget'])
            ->name('dearSanta.resend_target');
    });

    Route::get('/org/{draw}', [OrganizerController::class, 'view'])->name('organizerPanel')
        ->missing(fn () => response()->view('missingDraw', [], 404));

    Route::middleware('decrypt.iv:draw,mail_title')->group(function (): void {
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
            ->name('organizerPanel.changeParticipants');
        Route::post('/draw/{draw}/changeDate', [OrganizerController::class, 'changeDate'])
            ->name('organizerPanel.changeDate');
        Route::get('/org/{draw}/fetchState', [OrganizerController::class, 'fetchState'])
            ->name('organizerPanel.fetchState');
    });
    Route::middleware('decrypt.iv:participant,name')->group(function (): void {
        Route::post('/org/{draw}/{participant}/changeEmail', [OrganizerController::class, 'changeEmail'])
            ->name('organizerPanel.changeEmail');
    });
});

Route::get('/fix', [FixOrganizerController::class, 'view'])->name('fixOrganizer');
Route::post('/fix', [FixOrganizerController::class, 'handle'])->name('fixOrganizer.handle');

// Route::get( '/{status}', fn( int | null $status = null ) => Illuminate\Support\Facades\App::abort( $status ) )->where('status', '[0-9]{3}');
