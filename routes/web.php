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

//Route::fallback([ErrorController::class, 'pageNotFound'])->name('404');

Route::controller(SingleController::class)
    ->group(function () {
        Route::get('/faq', 'faq')->name('faq');
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/legal', 'legal')->name('legal');
    });

Route::controller(RandomFormController::class)
    ->name('form.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/process', 'handle')->name('process');
    });

Route::controller(JoinDrawController::class)
    ->group(function () {
        Route::get('/join/{pending_draw}', 'join')
            ->name('pending.join')
            ->missing(function () {
                return ErrorController::drawNotFound();
            });

        Route::post('/join/{pending_draw}', 'handleJoin')
            ->name('pending.handleJoin');
    });

Route::controller(DrawDashboardController::class)
    ->name('draw.')
    ->prefix('/draw/{draw}')
    ->group(function () {
        Route::get('/', 'index')->name('index')
            ->missing(function () {
                return ErrorController::drawNotFound();
            });

        Route::get('/confirm', 'confirmOrganizerEmail')->middleware('signed')->name('confirmOrganizerEmail');

        Route::middleware('decrypt.iv:draw,title')
            ->group(function () {
                Route::post('/title', 'changeTitle')->name('changeTitle');

                Route::post('/name', 'changeOrganizerName')->name('changeOrganizerName');
                Route::post('/email', 'changeOrganizerEmail')->name('changeOrganizerEmail');

                Route::post('/participants', 'addParticipantName')->name('addParticipantName');

                Route::post('/process', 'handle')->name('process');

                Route::post('/cancel', 'cancel')->middleware('signed')->name('cancel');
            });

        Route::scopeBindings()
            ->name('participant.')
            ->prefix('/participants/{participant}')
            ->middleware('decrypt.iv:participant,name')
            ->group(function () {
                Route::post('/name', 'changeParticipantName')->name('updateName');
                Route::post('/email', 'changeParticipantEmail')->name('changeEmail');

                Route::delete('/', 'removeParticipant')->name('remove');
            });
    });
/*
Route::controller(DearSantaController::class)
    ->middleware('signed')
    ->prefix('/santa/{participant}')
    ->name('santa.')
    ->group(function () {
        Route::get('/', 'index')->name('index')
            ->missing(function () {
                return ErrorController::drawNotFound();
            });

        Route::middleware('decrypt.iv:participant,name')
            ->group(function () {
                Route::get('/fetch', 'fetch')->name('fetch');
                Route::post('/sendSanta', 'handleSanta')->name('contactSanta');
                Route::post('/sendTarget', 'handleTarget')->name('contactTarget');
                Route::get('/resendSanta/{dearSanta}', 'resendDearSanta')->name('resendDearSanta');
                Route::get('/resendTarget/{dearTarget}', 'resendDearTarget')->name('resendDearTarget');
                Route::get('/target', 'resendTarget')->name('resendTarget');
            });
    });

Route::controller(ReportController::class)
    ->prefix('/report/{participant}')
    ->group(function () {
        Route::get('/', 'view')->name('report');
        Route::post('/', 'handle')->middleware('decrypt.iv:participant,name')->name('report.handle');
    });

Route::controller(OrganizerController::class)
    ->middleware('signed')
    ->prefix('/org/{draw}')
    ->name('organizer.')
    ->group(function () {
        Route::get('/', 'index')->name('index')
            ->missing(function () {
                return ErrorController::drawNotFound();
            });

        Route::middleware('decrypt.iv:draw,mail_title')->group(function () {
            Route::get('/fetch', 'fetch')->name('fetch');
            Route::delete('/', 'delete')->name('delete');
            Route::get('/csvInit', 'csvInit')->name('csvInit');
            Route::get('/csvFinal', 'csvFinal')->name('csvFinal');
        });

        Route::middleware('decrypt.iv:participant,name')->group(function () {
            Route::get('/{participant}/resend', 'resendTarget')->name('resendTarget');
            Route::post('/{participant}/changeEmail', 'changeEmail')->name('changeEmail');
            Route::post('/{participant}/changeName', 'changeName')->name('changeName');
            Route::get('/{participant}/withdraw', 'withdraw')->name('withdraw');
        });
    });
*/