<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix' => 'budget'], function(){
	Route::get('/', function(){ 
		return redirect()->route('docs.api');
	});

	Route::get('docs/{scaler?}', ['as' => 'docs.api', function($use_scaler = null){
		return view('docs.'.($use_scaler == "scaler" ? 'scaler' : 'api'), ['webapp' => 'budget']);
	}]);

	Route::post('user', 'UserController@create');

	Route::group(['middleware' => ['auth.user']], function(){
		Route::post('api_key', 'UserController@apiKeyCreate');
		Route::delete('api_key/{api_key}', 'UserController@apiKeyDestroy');
	});
	
	Route::group(['middleware' => ['auth.api']], function(){
		Route::post('account', 'AccountController@create');
		Route::get('account', 'AccountController@index');

		Route::get('account/{account_id}', 'AccountController@show');
		Route::patch('account/{account_id}', 'AccountController@update');
		Route::delete('account/{account_id}', 'AccountController@destroy');
		Route::get('account/{account_id}/accounts', 'AccountController@childaccounts');

		Route::get('account/{account_id}/transactions', 'TransactionController@index');
		Route::post('account/{account_id}/transactions', 'TransactionController@create');
		Route::delete('account/{account_id}/transactions/{transaction_id}', 'TransactionController@destroy');

		Route::put('account/{account_id}/budget', 'BudgetController@create');
		Route::get('account/{account_id}/budget', 'BudgetController@index');
		Route::delete('account/{account_id}/budget', 'BudgetController@destroy');

		Route::get('notifications', 'NotificationController@index');
	});
});

Route::post('signup', ['middleware' => 'account.creation', 'uses' => 'UserController@create']);
Route::get('signup', ['as' => 'auth.signup', function(){
	return view('auth.signup');
}]);

Route::post('login', 'UserController@login');
Route::get('login', ['as' => 'auth.login', function(){
	if(Auth::check()){
		return redirect()->route('dashboard');
	}
	return view('auth.login');
}]);

Route::get('/', function(){
	return redirect()->route('auth.login');
});

Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@dashboard']);

Route::any('{any}', function(){
	return view('error', ['status' => 404, 'message' => 'Not Found']);
})->where('any', '.*');