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

Route::get('/', function () {
    return view('randomForm');
});
Route::post('/', 'RandomFormController@handle');

Route::pattern('santa', '[0-9a-zA-Z]{'.config('hashids.connections')[config('hashids.default')]['length'].'}');
Route::get('/dearsanta/{santa}', 'DearSantaController@view')->name('dearsanta');
Route::post('/dearsanta/{santa}', 'DearSantaController@handle');

Route::pattern('draw', '[0-9a-zA-Z]{'.config('hashids.connections')[config('hashids.default')]['length'].'}');
Route::get('/admin/{draw}', 'OrganizerController@view')->name('organizer');
Route::post('/admin/{draw}', 'OrganizerConroller@handle');

Route::post('/event', 'EmailEventController@handle');

if ($this->app->environment('local', 'dev', 'testing')) {
    Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
}
