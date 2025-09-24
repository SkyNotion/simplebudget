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

Route::post('/user', 'Api\UserController@create');

Route::middleware('auth.basic')->post('/token', 'Api\ApiTokenController@create');

Route::middleware('auth:api')->group(function(){
    Route::get('/user', 'Api\UserController@show');

    Route::delete('/token', 'Api\ApiTokenController@destroy');

    Route::post('/account', 'Api\AccountController@create');
    Route::get('/account', 'Api\AccountController@index');

    Route::group(['prefix' => 'account/{account_id}', 'where' => ['account_id' => '[0-9]+']], function(){
        Route::get('/', 'Api\AccountController@show');
        Route::patch('/', 'Api\AccountController@update');
        Route::delete('/', 'Api\AccountController@destroy');

        Route::get('/accounts', 'Api\AccountController@index');

        Route::put('/transactions', 'Api\TransactionController@create');
        Route::get('/transactions', 'Api\TransactionController@index');
        Route::delete('/transactions/{transaction_id}', 'Api\TransactionController@destroy')
             ->where('transaction_id', '[0-9]+');

        Route::put('/budget', 'Api\BudgetController@create');
        Route::get('/budget', 'Api\BudgetController@index');
        Route::delete('/budget', 'Api\BudgetController@destroy');
    });
});