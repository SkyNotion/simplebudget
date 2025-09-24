<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Auth;
use Validator;
use App\Custom\Responses as CustomResponse;

use App\Http\Controllers\Controller;

class ApiTokenController extends Controller
{
    public function create(Request $request){
        $params = $request->only('name');
        $rv = Validator::make($params,
            ['name' => 'required|string|max:255']
        );

        if($rv->fails()){
            return CustomResponse::badRequest($rv->errors()->first());
        }

        $token = $request->user()->createToken($params['name']);
        return CustomResponse::json([
            'name' => $token->token->name,
            'token' => $token->accessToken,
            'created_at' => $token->token->created_at
        ], 201);
    }

    public function destroy(Request $request){
        $request->user()->token()->revoke();
    }
}
