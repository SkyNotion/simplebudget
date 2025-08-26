<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use Hash;

use App\User;
use App\ApiKey;

use App\Custom\Uuid;
use App\Custom\Responses;

class UserController extends Controller {

	public $validation_messages = [
		'name.required' => 'Name of the new user account is missing',
		'email.required' => 'Email is missing',
		'email.email' => 'Email not valid',
		'email.unique' => 'Email already exists',
		'password.required' => 'Password is missing'
	];

	public function create(Request $request)
	{	
		$params = $request->all();
		$rv = Validator::make($params,
			['name' => 'required',
			 'email' => 'required|email|unique:users,email',
			 'password' => 'required'],
			 $this->validation_messages
		);

		if($rv->fails()){
			return Responses::noRequestBody($rv->errors()->first());
		}

		$params['password'] = Hash::make($params['password']);
	    return Responses::json(User::create($params)->fresh(), 201);
	}

	public function api_key(Request $request, $api_key = null){
		if($api_key){
			if(ApiKey::where('user_id', $request->user_id)
					 ->where(function($query){
					 	$query->where('api_key', $api_key)
					 		  ->where('name', $api_key);
					 })->delete()){
				return Responses::message('Revoked', 200);
			}
			return Responses::apiKeyExists();
		}

		if(sizeof(ApiKey::where('user_id', $request->user_id)
						->where('name', $request->input('name'))
						->first())){
			return Responses::apiKeyExists();
		}

		return Responses::json(ApiKey::create([
			'user_id' => $request->user_id,
			'name' => $request->input('name'),
			'api_key' => Uuid::uuid4()
		])->fresh(), 201);
	}

}
