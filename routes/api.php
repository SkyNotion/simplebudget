<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/echo', function(Request $request){
    return $request;
});

Route::get('/docs', function(){
    return view('docs.api');
})->name('docs.api');

Route::get('/', function(){
    return redirect()->route('docs.api');
});

Route::post('/user', 'UserController@create');

Route::middleware('auth.basic')->post('/token', 'ApiTokenController@create');

Route::middleware('auth:api')->group(function(){
    Route::get('/user', 'UserController@show');

    Route::delete('/token', 'ApiTokenController@destroy');

    Route::post('/account', 'AccountController@create');
    Route::get('/account', 'AccountController@index');

    Route::group(['prefix' => 'account/{account_id}', 'where' => ['account_id' => '[0-9]+']], function(){
        Route::get('/', 'AccountController@show');
        Route::patch('/', 'AccountController@update');
        Route::delete('/', 'AccountController@destroy');

        Route::get('/accounts', 'AccountController@index');

        Route::put('/transactions', 'TransactionController@create');
        Route::get('/transactions', 'TransactionController@index');
        Route::delete('/transactions/{transaction_id}', 'TransactionController@destroy')
             ->where('transaction_id', '[0-9]+');

        Route::put('/budget', 'BudgetController@create');
        Route::get('/budget', 'BudgetController@index');
        Route::delete('/budget', 'BudgetController@destroy');
    });
});