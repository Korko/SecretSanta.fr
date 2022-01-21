<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\DearSantaController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\RandomFormController;
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

Route::view('/faq', 'faq')->name('faq');
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/legal', 'legal')->name('legal');

Route::controller(RandomFormController::class)
    ->name('form.')
    ->group(function () {
        Route::view('/', 'randomForm')->name('view');
        Route::post('/process', 'handle')->name('process');
    });


Route::controller(DearSantaController::class)
    ->middleware('signed')
    ->prefix('/santa/{participant}')
    ->name('santa.')
    ->group(function() {
        Route::get('/', function (Participant $santa) {
                return view('dearSanta', [
                    'participant' => $participant->hash,
                ]);
            })
            ->missing(function () {
                return response()->view('missingDraw', [], 404);
            })
            ->name('view');

        Route::middleware('decrypt.iv:participant,name')
            ->group(function () {
                Route::get('/fetch', 'fetch')->name('fetch');
                Route::post('/send', 'handle')->name('contact');
                Route::get('/{dearSanta}/resend', 'resend')->name('resend');
                Route::get('/resendTarget', 'resendTarget')->name('resend_target');
            });

        Route::post('/sub', function(Participant $participant, Request $request) {
                $participant->updatePushSubscription(
                    $request->input('endpoint'),
                    $request->input('keys.p256dh'),
                    $request->input('keys.auth')
                );

                return response()->json([
                    'success' => true
                ]);
            })
            ->name('sub');

        Route::post('/unsub', function(Participant $participant, Request $request) {
                $participant->deletePushSubscription($request->input('endpoint'));

                return response()->json([
                    'success' => true
                ]);
            })
            ->name('unsub');
    });

Route::controller(OrganizerController::class)
    ->middleware('signed')
    ->prefix('/org/{draw}')
    ->name('organizer.')
    ->group(function() {
        Route::get('/', function (Draw $draw) {
                return view('organizer', [
                    'draw' => $draw->hash,
                ]);
            })
            ->missing(function () {
                return response()->view('missingDraw', [], 404);
            })
            ->name('view');

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