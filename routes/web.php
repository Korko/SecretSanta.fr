<?php

use App\Http\Controllers\DearSantaController;
use App\Http\Controllers\DrawDashboardController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\FixOrganizerController;
use App\Http\Controllers\JoinDrawController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\RandomFormController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SingleController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
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
Route::get('/faq', [SingleController::class, 'faq'])->name('faq');
Route::get('/legal', [SingleController::class, 'legal'])->name('legal');

// TODO one day... Route::get('/dashboard', [SingleController::class, 'dashboard'])->name('dashboard');

// Visitor actions
Route::get('/', [RandomFormController::class, 'display'])->name('form.index');
Route::post('/process', [RandomFormController::class, 'handle'])->middleware([HandlePrecognitiveRequests::class])->name('form.process');

Route::get('/fix', [FixOrganizerController::class, 'view'])->name('fixOrganizer.index');
Route::post('/fix', [FixOrganizerController::class, 'handle'])->name('fixOrganizer.handle');

Route::middleware('signed')
    ->group(function () {
        Route::get('/join/{draw:ulid}', [JoinDrawController::class, 'display'])->name('pending.join');
        Route::post('/join/{draw:ulid}', [JoinDrawController::class, 'handle'])->middleware('decrypt.iv:draw,title')->name('pending.join.handle');

        Route::get('/report/{participant:ulid}', [ReportController::class, 'view'])->name('report.index');
        Route::post('/report/{participant:ulid}', [ReportController::class, 'handle'])->middleware('decrypt.iv:participant,name')->name('report.handle');
    });

// Participant actions
Route::get('/santa/{participant:ulid}', [DrawDashboardController::class, 'index'])->name('participant.index');
// Route::get('/santa/{participant:ulid}', [DearSantaController::class, 'index'])->name('participant.index');
Route::middleware(['decrypt.iv:participant,name', 'signed'])
    ->group(function () {
        Route::get('/santa/{participant:ulid}/fetch', [DrawDashboardController::class, 'fetch'])->name('participant.fetch');

        Route::post('/santa/{participant:ulid}/name', [DrawDashboardController::class, 'changeParticipantName'])->name('participant.changeName');
        Route::post('/santa/{participant:ulid}/email', [DrawDashboardController::class, 'changeParticipantEmail'])->name('participant.changeEmail');

        Route::get('/santa/{participant:ulid}/confirm', [DrawDashboardController::class, 'confirmParticipantEmail'])->name('participant.confirmEmail');

        Route::get('/santa/{participant:ulid}/target', [DrawDashboardController::class, 'resendTarget'])->name('participant.resendTarget');

        Route::delete('/santa/{participant:ulid}', [DrawDashboardController::class, 'removeParticipant'])->name('participant.withdraw');

        Route::post('/santa/{participant:ulid}/sendSanta', [DearSantaController::class, 'handleSanta'])->name('participant.contactSanta');
        Route::post('/santa/{participant:ulid}/sendTarget', [DearSantaController::class, 'handleTarget'])->name('participant.contactTarget');

        Route::get('/santa/{participant:ulid}/resendSanta/{dearSanta}', [DearSantaController::class, 'resendDearSanta'])->name('participant.resendDearSanta');
        Route::get('/santa/{participant:ulid}/resendTarget/{dearTarget}', [DearSantaController::class, 'resendDearTarget'])->name('participant.resendDearTarget');

    });

// Organizer actions
Route::middleware(['decrypt.iv:draw,title', 'signed'])
    ->group(function () {
        Route::post('/draw/{draw:ulid}/participate', [DrawDashboardController::class, 'participate'])->name('draw.participate');
        Route::post('/draw/{draw:ulid}/withdraw', [DrawDashboardController::class, 'withdraw'])->name('draw.withdraw');

        Route::post('/draw/{draw:ulid}/title', [DrawDashboardController::class, 'changeTitle'])->name('draw.changeTitle');

        Route::post('/draw/{draw:ulid}/participants', [DrawDashboardController::class, 'addParticipant'])->name('draw.addParticipant');
        Route::delete('/draw/{draw:ulid}/participants/{participant:ulid}', [DrawDashboardController::class, 'removeParticipant'])->scopeBindings()->name('draw.removeParticipant');

        Route::post('/draw/{draw:ulid}/process', [DrawDashboardController::class, 'handle'])->name('draw.processDraw');

        Route::post('/draw/{draw:ulid}/cancel', [DrawDashboardController::class, 'cancel'])->name('draw.cancelDraw');
    });
