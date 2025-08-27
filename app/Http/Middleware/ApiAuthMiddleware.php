<?php namespace App\Http\Middleware;

use Closure;
use Cache;
use Hash;

use App\ApiKey;

use App\Custom\Responses;

class ApiAuthMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		/* 
			NOTE: This method for authencating with hashed api keys 
			is not too bad (at a surface view of the way it works)
			but it could definitely be improved and optimization 
			so it goes through less steps is also good.
		*/
		$api_key = $request->header('api-key');
		if($api_key){
			try{
				$api_key = base64_decode($api_key);
			}catch(Exception $e){
				return Responses::apiAuthUnauthorized();
			}
			$api_key = explode('.', $api_key);
			if(sizeof($api_key) != 2){
				return Responses::apiAuthUnauthorized();
			}
			$ids = explode(':', $api_key[0]);
			if(sizeof($ids) != 2){
				return Responses::apiAuthUnauthorized();
			}
			$user_id = $ids[0];
			$key_id = $ids[1];
			$key = null;
			if(!Cache::has($api_key[0])){
				$key = ApiKey::where('user_id', $user_id)
							 ->where('key_id', $key_id)
							 ->first();
				if($key == null){
					return Responses::apiAuthUnauthorized();
				}
				Cache::put($api_key[0], $key->api_key, 60);
				$key = $key->api_key;
			}else{
				$key = Cache::get($api_key[0]);
			}
			if(!Hash::check($api_key[1], $key)){
				return Responses::apiAuthUnauthorized();
			}
			// Also validate account_id if present
			if(!is_null($request->route('account_id'))){
				if(!is_numeric($request->route('account_id'))){
					return Responses::invalidAccountId();
				}
			}
			$request->user_id = $user_id;
			return $next($request);
		}
		return Responses::apiAuthUnauthorized();
	}

}
