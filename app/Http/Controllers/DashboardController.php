<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;

class DashboardController extends Controller {

	public function dashboard(Request $request){
		if(!Auth::check()){
			return redirect()->route('auth.login', ['redirect' => $request->url()]);
		}
		return view('docs.api', ['webapp' => 'budget']);
	}

}
