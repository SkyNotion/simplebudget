<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use Hash;
use Auth;
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

	public function login(Request $request){
		$params = $request->all();
		$rv = Validator::make($params,
			['email' => 'required|email',
			 'password' => 'required'],
			['email.required' => 'Email is missing',
			 'email.email' => 'Email not valid',
			 'password.required' => 'Password is missing']
		);

		if($rv->fails()){
			return view('auth.login', [
				'message' => $rv->errors()->first(),
				'level' => 'error'
			]);
		}

		if(Auth::attempt(['email' => $params['email'], 'password' => $params['password']])){
			if($request->has('redirect')){
				return redirect()->intended($params['redirect']);
			}else{
				return redirect()->route('dashboard');
			}
		}else{
			return view('auth.login', [
				'message' => 'Email or password is wrong',
				'level' => 'error'
			]);
		}
	}

	public function apiKeyCreate(Request $request){
		if(!is_null(ApiKey::where('user_id', $request->user_id)
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
			'api_key' => str_replace('=', '', base64_encode("$request->user_id:$key->key_id:$api_key")),
			'created_at' => $key->created_at->toDateTimeString()
		], 201);
	}

	public function apiKeyDestroy(Request $request, $api_key = null){
		$key = ApiKey::where('user_id', $request->user_id)
					 ->where('name', $api_key)
					 ->first();
		if(!is_null($key)){
			if(Cache::has("$request->user_id:$key->key_id")){
				Cache::delete("$request->user_id:$key->key_id");
			}
			$key->delete();
			return Responses::message('Revoked', 200);
		}

		try{
			$api_key = base64_decode($api_key);
		}catch(Exception $e){
			return Responses::invalidRequest();
		}

		list($user_id, $key_id, $api_key) = explode(':', $api_key, 3);
		$key = ApiKey::where('user_id', $user_id)
				 	 ->where('key_id', $key_id)
				 	 ->first();
		if(!is_null($key)){
			if(Cache::has("$user_id:$key_id")){
				Cache::delete("$user_id:$key_id");
			}
			$key->delete();
			return Responses::message('Revoked', 200);
		}

		return Responses::invalidRequest();
	}
}
