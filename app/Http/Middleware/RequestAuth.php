<?php namespace App\Http\Middleware;

use Closure;
use Cache;
use Auth;
use Hash;

use App\User;
use App\ApiKey;

use App\Custom\Responses;

class RequestAuth {

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
        if(!is_null($request->header('api-key'))){
			/*
				NOTE: This method for authencating with hashed api keys 
				could definitely be improved and optimization 
				so it goes through less steps is also good.
			*/
			$api_key = $request->header('api-key');
			try{
				$api_key = base64_decode($api_key);
			}catch(Exception $e){
				return Responses::apiAuthUnauthorized();
			}

			list($user_id, $key_id, $api_key) = explode(':', $api_key, 3);
			if(!Cache::has("$user_id:$key_id")){
				$key = ApiKey::where('user_id', $user_id)
							 ->where('key_id', $key_id)
							 ->first();
				if(is_null($key)){
					return Responses::apiAuthUnauthorized();
				}
				Cache::put("$user_id:$key_id", $key->api_key, 60);
				$key = $key->api_key;
			}else{
				$key = Cache::get("$user_id:$key_id");
			}

			if(!Hash::check($api_key, $key)){
				return Responses::apiAuthUnauthorized();
			}
			$request->user_id = $user_id;
        }elseif(!is_null($request->header('authorization'))){
            list($identifier, $auth_string) = explode(' ', $request->header('authorization'), 2);
            if(strtolower($identifier) != 'basic'){
                return Responses::basicAuthUnauthorized();
            }

            list($email, $password) = explode(':', base64_decode($auth_string), 2);
            $user = User::where('email', '=', $email)->first();
            if(!is_null($user)){
                if(!Hash::check($password, $user['password'])){
                    return Responses::basicAuthUnauthorized();
                }
            }else{
                return Responses::basicAuthUnauthorized();
            }

            $request->user_id = $user['user_id'];
        }else{
            if(!Auth::check()){
                return redirect()->route('auth.login', ['redirect' => $request->url()]);
            }

            $request->user_id = Auth::user()->getAuthIdentifier();
        }
        return $next($request);
    }

}
