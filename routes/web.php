<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RandomFormController;
use App\Http\Controllers\DearSantaController;
use App\Http\Controllers\OrganizerController;

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

Route::pattern('draw:hash', '[0-9a-zA-Z]{'.config('hashids.connections.draw.length').',}');
Route::pattern('participant:hash', '[0-9a-zA-Z]{'.config('hashids.connections.santa.length').',}');

Route::get('/dearsanta/{participant:hash}', [DearSantaController::class, 'view'])->name('dearsanta');
Route::middleware(['decrypt.key:participant,name'])->group(function () {
    Route::post('/dearsanta/{participant:hash}', [DearSantaController::class, 'fetch'])->name('dearsanta.fetch');
    Route::post('/dearsanta/{participant:hash}/send', [DearSantaController::class, 'handle'])->name('dearsanta.contact');
    Route::post('/dearsanta/{participant:hash}/fetchState', [DearSantaController::class, 'fetchState'])->name('dearsanta.fetchState');
});

Route::get('/org/{draw:hash}', [OrganizerController::class, 'view'])->name('organizerPanel');
Route::middleware(['decrypt.key:draw,mail_title'])->group(function () {
    Route::post('/org/{draw:hash}', [OrganizerController::class, 'fetch'])->name('organizerPanel.fetch');
    Route::delete('/org/{draw:hash}', [OrganizerController::class, 'delete'])->name('organizerPanel.delete');
    Route::post('/org/{draw:hash}/fetchState', [OrganizerController::class, 'fetchState'])->name('organizerPanel.fetchState');
});
Route::middleware(['decrypt.key:participant,name'])->group(function () {
    Route::post('/org/{draw:hash}/{participant:id}/changeEmail', [OrganizerController::class, 'changeEmail'])->name('organizerPanel.changeEmail');
    Route::post('/org/{draw:hash}/{participant:id}/resendEmail', [OrganizerController::class, 'resendEmail'])->name('organizerPanel.resendEmail');
});
