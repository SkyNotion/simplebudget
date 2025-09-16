<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use Hash;
use Auth;

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

	    return response()->json(User::create($params)->attributesToArray(), 201);
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
			return response()->json(['error' => 'An api key with that name exists'], 400);
		}

		return response()->json(ApiKey::create([
			'user_id' => $request->user_id,
			'name' => $request->input('name'),
			'api_key' => Uuid::uuid4()
		])->attributesToArray(), 201);
	}

	public function apiKeyDestroy(Request $request, $api_key = null){
		if(ApiKey::where('user_id', $request->user_id)
				 ->where(function($query){
				 	$query->where('api_key', $api_key)
				 		  ->orWhere('name', $api_key);
				 })->delete()){
			return response('Revoked', 200);
		}
		return response()->json(['error' => 'Api key or name does not exist'], 400);
	}
}
