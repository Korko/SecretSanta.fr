<?php

use App\Http\Controllers\DearSantaController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\RandomFormController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SingleController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
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

RateLimiter::for('global', function (Request $request) {
    return Limit::perMinute(100)->by($request->ip())->response(function () {
        return abort(429);
    });
});

Route::pattern('draw', '[0-9a-zA-Z]{'.config('hashids.connections.draw.length').',}');
Route::pattern('participant', '[0-9a-zA-Z]{'.config('hashids.connections.santa.length').',}');
Route::pattern('dearSanta', '[0-9a-zA-Z]{'.config('hashids.connections.dearSanta.length').',}');
Route::pattern('dearTarget', '[0-9a-zA-Z]{'.config('hashids.connections.dearTarget.length').',}');

Route::fallback([ErrorController::class, 'pageNotFound'])->name('404');

// GET faq
// GET legal

// GET signup
// POST signup
// GET login #nojs
// POST login

// GET me (dashboard)

// GET new (nouveau tirage au sort)
// POST new (nouveau tirage au sort /!\ en cas de non ajax, peut retourner view pour page 2 et 3)

// GET join (rejoindre un tirage au sort)
// GET join/{token} (rejoindre un tirage au sort)
// PUT join/{token} (rejoindre un tirage au sort /!\ en cas de non ajax, peut retourner view pour page 2 et 3)

// GET draw/{draw} (page de tirage au sort)
// HEAD draw/{draw} (récupération des données du tirage au sort)
// PATCH draw/{draw} (édition du tirage au sort / fermeture / réouverture / lancer le tirage au sort)

// GET draw/{draw}/edit (page d'édition du tirage au sort)

// GET draw/{draw}/import (importer des participants) #nojs
// POST draw/{draw}/import (importer des participants)

// GET draw/{draw}/download (télécharger la liste des participants)

// GET draw/{draw}/delete (supprimer le tirage au sort) #nojs
// DELETE draw/{draw} (supprimer le tirage au sort)

// GET draw/{draw}/{participant} (supprimer un participant) #nojs
// DELETE draw/{draw}/{participant} (supprimer un participant)

// GET draw/{draw}/inbox
// POST draw/{draw}/inbox (envoi d'un message)

// GET draw/{draw}/report
// POST draw/{draw}/report (signaler un tirage au sort)

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
