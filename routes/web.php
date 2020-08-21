<?php

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

Route::get('/', 'RandomFormController@view');
Route::post('/', 'RandomFormController@handle');

Route::get('/faq', 'RandomFormController@faq')->name('faq');

Route::pattern('draw:hash', '[0-9a-zA-Z]{'.config('hashids.connections.draw.length').',}');
Route::pattern('participant:hash', '[0-9a-zA-Z]{'.config('hashids.connections.santa.length').',}');

Route::get('/dearsanta/{participant:hash}', 'DearSantaController@view')->name('dearsanta');
Route::middleware(['decrypt.key:participant,name'])->group(function () {
    Route::post('/dearsanta/{participant:hash}', 'DearSantaController@fetch')->name('dearsanta.fetch');
    Route::post('/dearsanta/{participant:hash}/send', 'DearSantaController@handle')->name('dearsanta.contact');
    Route::post('/dearsanta/{participant:hash}/fetchState', 'DearSantaController@fetchState')->name('dearsanta.fetchState');
});

Route::get('/org/{draw:hash}', 'OrganizerController@view')->name('organizerPanel');
Route::middleware(['decrypt.key:draw,mail_title'])->group(function () {
    Route::post('/org/{draw:hash}', 'OrganizerController@fetch')->name('organizerPanel.fetch');
    Route::delete('/org/{draw:hash}', 'OrganizerController@delete')->name('organizerPanel.delete');
    Route::post('/org/{draw:hash}/fetchState', 'OrganizerController@fetchState')->name('organizerPanel.fetchState');
});
Route::middleware(['decrypt.key:participant,name'])->group(function () {
    Route::post('/org/{draw:hash}/{participant:id}/changeEmail', 'OrganizerController@changeEmail')->name('organizerPanel.changeEmail');
    Route::post('/org/{draw:hash}/{participant:id}/resendEmail', 'OrganizerController@resendEmail')->name('organizerPanel.resendEmail');
});
