<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;

class DashboardController extends Controller {

	public function dashboard(Request $request){
		return view('docs.api');
	}

}
