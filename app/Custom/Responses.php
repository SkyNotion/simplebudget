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
		return Responses::error('Api key or name does not exist', 400);
	}
}