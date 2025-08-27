<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use Hash;
use Cache;

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
			// first we check if it's the name of the api key 
			$key = ApiKey::where('user_id', $request->user_id)
					 	 ->where('name', $api_key)
					 	 ->first();
			if(!is_null($key)){
				if(Cache::has($request->user_id.':'.$key->key_id)){
					Cache::delete($request->user_id.':'.$key->key_id);
				}
				$key->delete();
				return Responses::message('Revoked', 200);
			}

			// if it is not the api key name
			try{
				$api_key = base64_decode($api_key);
			}catch(Exception $e){
				return Responses::invalidRequest();
			}
			$api_key = explode('.', $api_key);
			if(sizeof($api_key) != 2){
				return Responses::invalidRequest();
			}
			$ids = explode(':', $api_key[0]);
			if(sizeof($ids) != 2){
				return Responses::invalidRequest();
			}
			$key = ApiKey::where('user_id', $ids[0])
					 	 ->where('key_id', $ids[1])
					 	 ->first();
			if(is_null($key)){
				return Responses::invalidRequest();
			}

			if(Cache::has($api_key[0])){
				Cache::delete($api_key[0]);
			}
			$key->delete();
			return Responses::message('Revoked', 200);
		}

		if(sizeof(ApiKey::where('user_id', $request->user_id)
						->where('name', $request->input('name'))
						->first())){
			return Responses::apiKeyExists();
		}

		$api_key = Uuid::uuid4();
		$key = ApiKey::create([
			'user_id' => $request->user_id,
			'name' => $request->input('name'),
			'api_key' => Hash::make($api_key)
		])->fresh();

		return Responses::json([
			'user_id' => $request->user_id,
			'name' => $key->name,
			'api_key' => str_replace('=', '', base64_encode($request->user_id.':'.$key->key_id.'.'.$api_key)),
			'created_at' => $key->created_at->toDateTimeString()
		], 201);
	}

}
