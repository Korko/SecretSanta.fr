<?php

use App\Models\Draw;
use App\Models\Participant;
use App\Models\PendingDraw;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DearSantaController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\RandomFormController;
use App\Http\Controllers\SingleController;
use App\Http\Controllers\StartController;
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

Route::fallback([ErrorController::class, 'pageNotFound'])->name('404');

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

Route::controller(StartController::class)
    ->middleware('signed')
    ->prefix('/pending/{pending}')
    ->name('pending.')
    ->group(function() {
        Route::get('/', 'index')->name('view');

        Route::middleware('decrypt.iv:pending,organizer_email')
            ->group(function () {
                Route::get('/fetch', 'fetch')->name('fetch');
                Route::get('/process', 'process')->name('process');
            });
    });

Route::controller(DearSantaController::class)
    ->middleware('signed')
    ->prefix('/santa/{participant}')
    ->name('santa.')
    ->group(function() {
        Route::get('/', 'index')->name('index')
            ->missing([ErrorController::class, 'drawNotFound']);

        Route::middleware('decrypt.iv:participant,name')
            ->group(function () {
                Route::get('/fetch', 'fetch')->name('fetch');
                Route::post('/send', 'handle')->name('contact');
                Route::get('/{dearSanta}/resend', 'resend')->name('resend');
                Route::get('/resendTarget', 'resendTarget')->name('resendTarget');
            });
    });

Route::controller(OrganizerController::class)
    ->middleware('signed')
    ->prefix('/org/{draw}')
    ->name('organizer.')
    ->group(function() {
        Route::get('/', 'index')->name('index')
            ->missing([ErrorController::class, 'drawNotFound']);

        Route::middleware('decrypt.iv:draw,mail_title')->group(function () {
            Route::get('/fetch', 'fetch')->name('fetch');
            Route::delete('/', 'delete')->name('delete');
            Route::get('/csvInit', 'csvInit')->name('csvInit');
            Route::get('/csvFinal', 'csvFinal')->name('csvFinal');
        });

        Route::middleware('decrypt.iv:participant,name')->group(function () {
            Route::post('/{participant}/changeEmail', 'changeEmail')->name('changeEmail');
            Route::post('/{participant}/changeName', 'changeName')->name('changeName');
            Route::get('/{participant}/withdraw', 'withdraw')->name('withdraw');
        });
    });
