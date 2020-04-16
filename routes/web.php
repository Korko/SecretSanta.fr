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
Route::post('/', 'RandomFormController@handle'); //->middleware('decrypt.input');

Route::get('/faq', 'RandomFormController@faq')->name('faq');

Route::pattern('participant', '[0-9a-zA-Z]{'.config('hashids.connections.santa.length').'}');

Route::get('/dearsanta/{participant}', 'DearSantaController@view')->name('dearsanta');
Route::middleware(['decrypt.key'])->group(function () {
    Route::post('/dearsanta/{participant}', 'DearSantaController@fetch')->name('dearsanta.fetch');
    Route::post('/dearsanta/{participant}/send', 'DearSantaController@handle')->name('dearsanta.contact');
    Route::post('/dearsanta/{participant}/fetchState', 'DearSantaController@fetchState')->name('dearsanta.fetchState');
});

Route::pattern('draw', '[0-9a-zA-Z]{'.config('hashids.connections.draw.length').'}');

Route::get('/org/{draw}', 'OrganizerController@view')->name('organizerPanel');
Route::middleware(['decrypt.key'])->group(function () {
    Route::post('/org/{draw}', 'OrganizerController@fetch')->name('organizerPanel.fetch');
    Route::post('/org/{draw}/fetchState', 'OrganizerController@fetchState')->name('organizerPanel.fetchState');
    Route::post('/org/{draw}/{participant}/changeEmail', 'OrganizerController@changeEmail')->name('organizerPanel.changeEmail');
    Route::post('/org/{draw}/{participant}/resendEmail', 'OrganizerController@resendEmail')->name('organizerPanel.resendEmail');
});
