<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use Hash;

use App\User;
use App\ApiKey;

use App\Custom\Uuid;

class UserController extends Controller {

	public $validation_messages = [
		'name.required' => 'Name of the new user account is missing',
		'email.required' => 'Email is missing',
		'email.email' => 'Email not valid',
		'email.unique' => 'Email already exists',
		'password.required' => 'Password is missing'
	];

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
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
			return response()->json(['error' => $rv->errors()->first()], 400);
		}

		$params['password'] = Hash::make($params['password']);

	    return response()->json(array_intersect_key(User::create($params)->attributesToArray(), ['user_id' => '', 'name' => '', 'email' => '']), 201);
	}

	public function api_key(Request $request, $api_key = null){
		if($api_key){
			if(ApiKey::whereRaw('user_id = ? and (api_key = ? or name = ?)',
				[$request->user_id, $api_key, $api_key])->delete()){
				return response('Revoked', 200);
			}
			return response()->json(['error' => 'Api key or name does not exist'], 400);
		}

		if(sizeof(ApiKey::whereRaw('user_id = ? and name = ?', 
			[$request->user_id, $request->input('name')])->first())){
			return response()->json(['error' => 'An api key with that name exists'], 400);
		}

		return response()->json(ApiKey::create([
			'user_id' => $request->user_id,
			'name' => $request->input('name'),
			'api_key' => Uuid::uuid4()
		])->attributesToArray(), 201);
	}

}
