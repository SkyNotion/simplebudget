<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function redirect(Request $request){
        return redirect()->route('dashboard');
    }
}
