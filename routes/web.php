<?php

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
Route::post('/', 'RandomFormController@handle');//->middleware('decrypt.input');

Route::pattern('santa', '[0-9a-zA-Z]{'.config('hashids.connections')[config('hashids.default')]['length'].'}');
Route::get('/dearsanta/{santa}', 'DearSantaController@view')->name('dearsanta');
Route::post('/dearsanta/{santa}', 'DearSantaController@handle');

Route::get('/org/{draw}', 'OrganizerController@view')->name('organizerPanel');
Route::post('/org/{draw}/{participant}/changeEmail', 'OrganizerController@changeEmail')->name('organizerPanel.changeEmail');

Route::post('/event', 'EmailEventController@handle');

if (App::environment('local', 'dev', 'testing')) {
    Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
}
