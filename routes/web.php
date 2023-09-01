<?php

use App\Http\Controllers\DearSantaController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\PendingController;
use App\Http\Controllers\RandomFormController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SingleController;
use App\Models\PendingDraw;
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
Route::pattern('pendingDraw', '[0-9a-zA-Z]{'.config('hashids.connections.pendingDraw.length').',}');
Route::pattern('pendingParticipant', '[0-9a-zA-Z]{'.config('hashids.connections.pendingParticipant.length').',}');
Route::pattern('participant', '[0-9a-zA-Z]{'.config('hashids.connections.participant.length').',}');
Route::pattern('dearSanta', '[0-9a-zA-Z]{'.config('hashids.connections.dearSanta.length').',}');
Route::pattern('dearTarget', '[0-9a-zA-Z]{'.config('hashids.connections.dearTarget.length').',}');

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

Route::model('pendingDraw', PendingDraw::class);
Route::get('/join/{pendingDraw}', [PendingController::class, 'join'])
    ->name('pending.join')
    ->missing(function () {
        return ErrorController::drawNotFound();
    });

Route::controller(PendingController::class)
    ->name('pending.')
    ->prefix('/pending/{pendingDraw}')
    ->group(function () {
        Route::get('/', 'index')->name('index')
            ->missing(function () {
                return ErrorController::drawNotFound();
            });

        // TODO: split into get/post with signed middleware?
        Route::get('/confirm', 'confirmOrganizerEmail')->middleware('signed')->name('confirmOrganizerEmail');

        Route::middleware('decrypt.iv:pendingDraw,title')
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
            ->prefix('/participants/{pendingParticipant}')
            ->middleware('decrypt.iv:pendingParticipant,name')
            ->group(function () {
                Route::post('/name', 'changeParticipantName')->name('updateName');
                Route::post('/email', 'changeParticipantEmail')->name('changeEmail');

                Route::delete('/', 'removeParticipant')->name('remove');
            });
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
