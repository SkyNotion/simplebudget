<?php namespace App\Http\Middleware;

use Closure;
use Validator;
use Hash;

use App\User;

use App\Custom\Responses;

class UserAuthMiddleware {

	public $validation_messages = [
		'email.required' => 'Email is missing',
		'email.email' => 'Email not valid',
		'password.required' => 'Password is missing'
	];

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{	
		$params = $request->all();
		$rv = Validator::make($params,
			['email' => 'required|email',
			 'password' => 'required'],
			 $this->validation_messages
		);

		if($rv->fails()){
			return Responses::error($rv->errors()->first(), 400);
		}

		$user = User::where('email', '=', $params['email'])->first();

		if(sizeof($user)){
			if(!Hash::check($params['password'], $user['password'])){
				return Responses::basicAuthUnauthorized();
			}
		}else{
			return Responses::basicAuthUnauthorized();
		}

		$request->user_id = $user['user_id'];

		return $next($request);
	}

}
