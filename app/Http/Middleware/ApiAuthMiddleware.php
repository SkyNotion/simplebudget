<?php namespace App\Http\Middleware;

use Closure;
use Cache;

use App\ApiKey;

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
		$unauthorized = response()->json(['error' => 'Unauthorized, api key is missing or invalid'], 401);
		$api_key = $request->header('api-key');
		if($api_key){
			if(!Cache::has($api_key)){
				$apk = ApiKey::where('api_key', '=', $api_key)->first();
				if(!sizeof($apk)){
					return $unauthorized;
				}
				Cache::put($api_key, $apk->user_id, 120);
			}
			$request->user_id = Cache::get($api_key);
			return $next($request);
		}
		return $unauthorized;
	}

}
