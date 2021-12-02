<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DearSantaController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\RandomFormController;

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
Route::post('/', [RandomFormController::class, 'handle']);

Route::get('/faq', [RandomFormController::class, 'faq'])->name('faq');
Route::get('/legal', function () {
    return response()->view('legal');
})->name('legal');

Route::pattern('draw', '[0-9a-zA-Z]{'.config('hashids.connections.draw.length').',}');
Route::pattern('participant', '[0-9a-zA-Z]{'.config('hashids.connections.santa.length').',}');
Route::pattern('dearSanta', '[0-9a-zA-Z]{'.config('hashids.connections.dearSanta.length').',}');
Route::pattern('mail:id', '[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}'); //UUID

Route::middleware('signed')->group(function() {
    Route::get('/dearSanta/{participant}', [DearSantaController::class, 'view'])
        ->name('dearSanta')
        ->missing(function () {
            return response()->view('missingDraw', [], 404);
        });

    Route::middleware('decrypt.iv:participant,name')->group(function () {
        Route::get('/participant/{participant}', [DearSantaController::class, 'fetch'])
            ->name('dearSanta.fetch');
        Route::post('/dearSanta/{participant}', [DearSantaController::class, 'handle'])
            ->name('dearSanta.contact');
        Route::get('/dearSanta/{participant}/fetchState', [DearSantaController::class, 'fetchState'])
            ->name('dearSanta.fetchState');
        Route::get('/dearSanta/{participant}/{dearSanta}/resend', [DearSantaController::class, 'resend'])
            ->name('dearSanta.resend');

        Route::post('/participant/{participant}/sub', function(Participant $participant, Request $request) {
            $participant->updatePushSubscription(
                $request->input('endpoint'),
                $request->input('keys.p256dh'),
                $request->input('keys.auth')
            );

            return response()->json([
                'success' => true
            ]);
        })->name('participant.sub');

        Route::post('/participant/{participant}/unsub', function(Participant $participant, Request $request) {
            $participant->deletePushSubscription($request->input('endpoint'));

            return response()->json([
                'success' => true
            ]);
        })->name('participant.unsub');
    });

    Route::get('/org/{draw}', [OrganizerController::class, 'view'])->name('organizerPanel')
        ->missing(function () {
            return response()->view('missingDraw', [], 404);
        });

    Route::middleware('decrypt.iv:draw,mail_title')->group(function () {
        Route::get('/draw/{draw}', [OrganizerController::class, 'fetch'])
            ->name('organizerPanel.fetch');
        Route::delete('/draw/{draw}', [OrganizerController::class, 'delete'])
            ->name('organizerPanel.delete');
        Route::get('/draw/{draw}/csvInit', [OrganizerController::class, 'csvInit'])
            ->name('organizerPanel.csvInit');
        Route::get('/draw/{draw}/csvFinal', [OrganizerController::class, 'csvFinal'])
            ->name('organizerPanel.csvFinal');
        Route::get('/org/{draw}/fetchState', [OrganizerController::class, 'fetchState'])
            ->name('organizerPanel.fetchState');
    });
    Route::middleware('decrypt.iv:participant,name')->group(function () {
        Route::post('/org/{draw}/{participant}/changeEmail', [OrganizerController::class, 'changeEmail'])
            ->name('organizerPanel.changeEmail');
    });

    Route::get('/email/{mail:notification}.png', [MailController::class, 'updateStatus'])
        ->name('pixel');
});
