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

Auth::routes();

Route::middleware('auth:web')->group(function(){
    Route::get('/', 'Web\DashboardController@redirect');
    Route::get('/home', 'Web\DashboardController@redirect');

    Route::get('/dashboard', function(){
        return view('docs.api');
    })->name('dashboard');
});