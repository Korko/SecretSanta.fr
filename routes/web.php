<?php

use App\Http\Controllers\DearSantaController;
use App\Http\Controllers\DrawDashboardController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\JoinDrawController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\RandomFormController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SingleController;
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

Route::get('/', [RandomFormController::class, 'display'])->name('form.index');
Route::post('/process', [RandomFormController::class, 'handle'])->name('form.process');

Route::get('/signup', [SignUpController::class, 'display'])->name('signup');
Route::post('/signup', [SignUpController::class, 'handle'])->name('signup.handle');
Route::get('/login', [AuthController::class, 'display'])->name('login');
Route::post('/login', [AuthController::class, 'handleIn'])->name('login.handle');
Route::post('/logout', [AuthController::class, 'handleOut'])->name('logout.handle');

Route::get('/faq', [SingleController::class, 'faq'])->name('faq');
Route::get('/dashboard', [SingleController::class, 'dashboard'])->name('dashboard');
Route::get('/legal', [SingleController::class, 'legal'])->name('legal');

Route::get('/join/{draw:ulid}', [JoinDrawController::class, 'display'])->name('pending.join');
Route::post('/join/{draw:ulid}', [JoinDrawController::class, 'handle'])->name('pending.join.handle');

Route::get('/draw/{draw:ulid}', [DrawDashboardController::class, 'index'])->name('draw.index');
Route::get('/draw/{draw:ulid}/confirm', [DrawDashboardController::class, 'confirmOrganizerEmail'])->middleware('signed')->name('draw.confirmOrganizerEmail');
Route::middleware('decrypt.iv:draw,title')
    ->group(function () {
        Route::post('/draw/{draw:ulid}/participate', [DrawDashboardController::class, 'participate'])->name('draw.participate');
        Route::post('/draw/{draw:ulid}/withdraw', [DrawDashboardController::class, 'withdraw'])->name('draw.withdraw');

        Route::post('/draw/{draw:ulid}/title', [DrawDashboardController::class, 'changeTitle'])->name('draw.changeTitle');

        Route::post('/draw/{draw:ulid}/name', [DrawDashboardController::class, 'changeOrganizerName'])->name('draw.changeOrganizerName');
        Route::post('/draw/{draw:ulid}/email', [DrawDashboardController::class, 'changeOrganizerEmail'])->name('draw.changeOrganizerEmail');

        Route::post('/draw/{draw:ulid}/participants', [DrawDashboardController::class, 'addParticipant'])->name('draw.participant.add');

        Route::post('/draw/{draw:ulid}/process', [DrawDashboardController::class, 'handle'])->name('draw.process');

        Route::post('/draw/{draw:ulid}/cancel', [DrawDashboardController::class, 'cancel'])->middleware('signed')->name('draw.cancel');

        Route::scopeBindings()
            ->group(function () {
                Route::post('/draw/{draw:ulid}/participants/{participant:ulid}/name', 'changeParticipantName')->name('draw.participant.updateName');
                Route::post('/draw/{draw:ulid}/participants/{participant:ulid}/email', 'changeParticipantEmail')->name('draw.participant.changeEmail');

                Route::delete('/draw/{draw:ulid}/participants/{participant:ulid}', 'removeParticipant')->name('draw.participant.remove');
            });
    });

/*
Route::middleware('signed')
    ->group(function () {
        Route::get('/santa/{participant:ulid}', [DearSantaController::class, 'index'])->name('santa.index');

        Route::middleware('decrypt.iv:participant,name')
            ->group(function () {
                Route::get('/santa/{participant:ulid}/fetch', [DearSantaController::class, 'fetch'])->name('santa.fetch');
                Route::post('/santa/{participant:ulid}/sendSanta', [DearSantaController::class, 'handleSanta'])->name('santa.contactSanta');
                Route::post('/santa/{participant:ulid}/sendTarget', [DearSantaController::class, 'handleTarget'])->name('santa.contactTarget');
                Route::get('/santa/{participant:ulid}/resendSanta/{dearSanta}', [DearSantaController::class, 'resendDearSanta'])->name('santa.resendDearSanta');
                Route::get('/santa/{participant:ulid}/resendTarget/{dearTarget}', [DearSantaController::class, 'resendDearTarget'])->name('santa.resendDearTarget');
                Route::get('/santa/{participant:ulid}/target', [DearSantaController::class, 'resendTarget'])->name('santa.resendTarget');
            });
    });

Route::get('/report/{participant:ulid}', [ReportController::class, 'view'])->name('report');
Route::post('/report/{participant:ulid}', [ReportController::class, 'handle'])->middleware('decrypt.iv:participant,name')->name('report.handle');

Route::middleware('signed')
    ->group(function () {
        Route::get('/org/{draw:ulid}', [OrganizerController::class, 'index'])->name('organizer.index');

        Route::middleware('decrypt.iv:draw,mail_title')->group(function () {
            Route::get('/org/{draw:ulid}/fetch', [OrganizerController::class, 'fetch'])->name('organizer.fetch');
            Route::delete('/org/{draw:ulid}', [OrganizerController::class, 'delete'])->name('organizer.delete');
            Route::get('/org/{draw:ulid}/csvInit', [OrganizerController::class, 'csvInit'])->name('organizer.csvInit');
            Route::get('/org/{draw:ulid}/csvFinal', [OrganizerController::class, 'csvFinal'])->name('organizer.csvFinal');

            Route::get('/org/{draw:ulid}/{participant:ulid}/resend', [OrganizerController::class, 'resendTarget'])->name('organizer.resendTarget');
            Route::post('/org/{draw:ulid}/{participant:ulid}/changeEmail', [OrganizerController::class, 'changeEmail'])->name('organizer.changeEmail');
            Route::post('/org/{draw:ulid}/{participant:ulid}/changeName', [OrganizerController::class, 'changeName'])->name('organizer.changeName');
            Route::get('/org/{draw:ulid}/{participant:ulid}/withdraw', [OrganizerController::class, 'withdraw'])->name('organizer.withdraw');
        });
    });
*/
