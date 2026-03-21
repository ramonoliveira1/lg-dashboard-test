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

Route::get('/', 'DashboardController@index')->name('dashboard');

Route::prefix('analysis')->name('analysis.')->group(function () {
    Route::get('/status', 'AnalysisController@status')->name('status');
    Route::post('/configure', 'AnalysisController@configure')->name('configure');
    Route::post('/generate', 'AnalysisController@generate')->name('generate');
});
