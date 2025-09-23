<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Hash;
use Validator;
use App\User;
use App\Custom\Responses as CustomResponse;

class UserController extends Controller
{
    public function create(Request $request){
        $params = $request->only('name', 'email', 'password');
        $rv = Validator::make($params,
            ['name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'password' => 'required|string|min:8']
        );

        if($rv->fails()){
            return CustomResponse::badRequest($rv->errors()->first());
        }

        $params['password'] = Hash::make($params['password']);
        return CustomResponse::json(User::create($params)->toArray(), 201);
    }

    public function show(Request $request){
        return $request->user();
    }
}
