<?php namespace App\Custom;

class Responses{

	public static function json($_array, $status_code = 200){
		return response()->json($_array, $status_code);
	}

	public static function message($message, $status_code = 200){
		return $this->json(['message' => $message], $status_code);
	}

	public static function error($message, $status_code){
		return $this->json(['error' => $message], $status_code);
	}

	public static function success(){
		return $this->message('Successful', 200);
	}

	public static function noContent(){
		return response('No content found', 204);
	}

	public static function noAccount(){
		return $this->error('Account does not exist', 404);
	}

	public static function noParent(){
		return $this->error('Parent account does not exists', 404);
	}

	public static function noChildAccounts(){
		return $this->error('No child accounts found', 404);
	}

	public static function noBudget(){
		return response('No budget found', 204);
	}

	public static function noRequestBody($message = 'Request body cannot be empty'){
		return $this->error($message, 400);
	}

	public static function noTransaction(){
		return $this->error('Account or transaction does not exist', 404);
	}

	public static function apiKeyExists(){
		return $this->error('Api key or name does not exist', 400);
	}
}