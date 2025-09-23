<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;
use App\Custom\Responses as CustomResponse;

class AccountController extends Controller
{
    public function validator($user_id, $required = true){
        return Validator::make($body,
            ['name' => (!$required ?: 'required|').'string|max:255',
             'parent_id' => [
                'numeric',
                Rule::exists('account')->where(function($query){
                    $query->('user_id', $user_id);
                }),
             ],
             'opening_balance' => 'numeric',
             'balance_limit' => 'numeric',
             'currency' => 'string|max:3',
             'type' => 'string',
             'description' => 'string']
        );
    }

    public function create(Request $request){
        $body = $request->all();
        $rv = validator($request->user()->user_id);

        if($rv->fails()){
            return CustomResponse::badRequest($rv->errors()->first());
        }

        $account = $request->user()
                           ->accounts()
                           ->create($body);
        return CustomResponse::json($account->toArray(), 201);
    }

    public function update(Request $request, $account_id = null){
        $body = $request->except('user_id');
        $rv = validator($request->user()->user_id, false);

        if($rv->fails()){
            return CustomResponse::badRequest($rv->errors()->first());
        }

        return $request->user()
                       ->account($account_id);
                       ->update($body)->fresh();
    }

    public function show(Request $request, $account_id = null){
        return $request->user()->account($account_id);
    }

    public function index(Request $request, $account_id = null){
        return $request->user()
                       ->accounts()
                       ->where('parent_id', $account_id)->get();
    }

    public function destroy(Request $request, $account_id = null){
        $request->user()->account($account_id)->delete();
    }
}
