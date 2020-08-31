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

Route::middleware(['signed'])->group(function () {

	Route::get('/dearsanta/{participant:hash}', [DearSantaController::class, 'view'])->name('dearsanta');#TODO: Use signed urls?
	Route::middleware(['decrypt.key:participant,name'])->group(function () {
	    Route::post('/dearsanta/{participant:hash}', [DearSantaController::class, 'fetch'])->name('dearsanta.fetch');
	    Route::post('/dearsanta/{participant:hash}/send', [DearSantaController::class, 'handle'])->name('dearsanta.contact');
	    Route::post('/dearsanta/{participant:hash}/fetchState', [DearSantaController::class, 'fetchState'])->name('dearsanta.fetchState');
	});

	#TODO: Weak security with same key for organizer and participants (dear santa) so only protection is hash

	Route::get('/draw/{draw:hash}', [OrganizerController::class, 'view'])->name('organizerPanel');
	Route::middleware(['decrypt.key:draw,mail_title'])->group(function () {
	    Route::post('/draw/{draw:hash}', [OrganizerController::class, 'fetch'])->name('organizerPanel.fetch');
	    Route::delete('/draw/{draw:hash}', [OrganizerController::class, 'delete'])->name('organizerPanel.delete');
	    Route::post('/draw/{draw:hash}/fetchState', [OrganizerController::class, 'fetchState'])->name('organizerPanel.fetchState');
	});
	#TODO: use /participant instead of /draw but different system from /dearsanta maybe signed route?
	Route::middleware(['decrypt.key:participant,name'])->group(function () {
	    Route::post('/draw/{draw:hash}/{participant:id}/changeEmail', [OrganizerController::class, 'changeEmail'])->name('organizerPanel.changeEmail');
	    Route::post('/draw/{draw:hash}/{participant:id}/resendEmail', [OrganizerController::class, 'resendEmail'])->name('organizerPanel.resendEmail');
	});

});