<?php namespace App\Http\Middleware;

use Closure;

class AccountCreationCheck {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$response = $next($request);
		if($response->getStatusCode() == 201){
			return redirect()->route('auth.login')->with([
				'message' => 'Account Created Successfully, Login',
				'level' => 'success'
			]);
		}elseif($response->getStatusCode() == 400){
			return view('auth.signup', [
				'message' => json_decode($response->getContent(), true)['error'],
				'level' => 'error'
			]);
		}
		return view('auth.signup', [
			'message' => 'Unknown Error Occured',
			'level' => 'error'
		]);
	}

}
