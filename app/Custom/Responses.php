<?php namespace App\Custom;

class Responses{

	public static function json($_array, $status_code = 200){
		return response()->json($_array, $status_code);
	}

	public static function message($message, $status_code = 200){
		return self::json(['message' => $message], $status_code);
	}

	public static function error($message, $status_code){
		return self::json(['error' => $message], $status_code);
	}

	public static function success(){
		return self::message('Successful', 200);
	}

	public static function noContent(){
		return response('No content found', 204);
	}

	public static function noAccount(){
		return self::error('Account does not exist', 404);
	}

	public static function noParent(){
		return self::error('Parent account does not exists', 404);
	}

	public static function noChildAccounts(){
		return self::error('No child accounts found', 404);
	}

	public static function noBudget(){
		return response('No budget found', 204);
	}

	public static function noRequestBody($message = 'Request body cannot be empty'){
		return self::error($message, 400);
	}

	public static function noTransaction(){
		return self::error('Account or transaction does not exist', 404);
	}

	public static function apiKeyExists(){
		return self::error('Api key with that name already exist', 400);
	}

	public static function basicAuthUnauthorized(){
		return self::error('Unauthorized, invalid email or password', 401);
	}

	public static function apiAuthUnauthorized(){
		return self::error('Unauthorized, api key is missing or invalid', 401);
	}

	public static function invalidRequest(){
		return self::error('Invalid request', 400);
	}

	public static function invalidAccountId(){
		return self::error('Invalid account id', 400);
	}
}