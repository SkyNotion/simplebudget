<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;
use App\Custom\Responses as CustomResponse;

use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function validator($body, $user_id, $required = true){
        return Validator::make($body,
            ['name' => (!$required ?: 'required|').'string|max:255',
             'parent_id' => [
                'numeric',
                Rule::exists('accounts', 'id')->where(function($query) use ($user_id){
                    $query->where('user_id', $user_id);
                })
             ],
             'balance' => 'numeric',
             'balance_limit' => 'numeric',
             'currency' => 'string|max:3',
             'type' => 'string',
             'description' => 'string']
        );
    }

    public function create(Request $request){
        $body = $request->all();
        $rv = $this->validator($body, $request->user()->id);

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
        $rv = $this->validator($body, $request->user()->id, false);

        if($rv->fails()){
            return CustomResponse::badRequest($rv->errors()->first());
        }

        $account = $request->user()->findAccountOrFail($account_id);
        $account->update($body);
        return $account->fresh();
    }

    public function show(Request $request, $account_id = null){
        return $request->user()->findAccountOrFail($account_id);
    }

    public function index(Request $request, $account_id = null){
        return $request->user()
                       ->accounts()
                       ->where('parent_id', $account_id)->get();
    }

    public function destroy(Request $request, $account_id = null){
        $request->user()->findAccountOrFail($account_id)->delete();
    }
}
