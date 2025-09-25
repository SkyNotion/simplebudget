<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Validator;
use App\Budget;
use App\Custom\Responses as CustomResponse;

use App\Http\Controllers\Controller;

class BudgetController extends Controller
{
    public function create(Request $request, $account_id = null){
        $account = $request->user()->findAccountOrFail($account_id);
        $body = $request->except('account_id', 'budget', 'balance');
        $rv = Validator::make($body,
            ['name' => 'string|max:255|nullable'
             'description' => 'string|nullable',
             'entities' => 'array|nullable']
        );

        if($rv->fails()){
            return CustomResponse::badRequest($rv->errors()->first());
        }

        if($body->has('entities')){
            $body['budget'] = 0.0;
            foreach($body['entities'] as $entity){
                $body['budget'] += $entity['amount'];
            }
            $body['entities'] = json_encode($body['entities']);
        }

        $body['account_id'] = $account_id;
        $budget = $account->budget();
        if(!isset($budget)){
           $body['balance'] = 0.0;
           $budget = Budget::create($body)->fresh(); 
        }else{
            $budget->update($body)->refresh();
        }

        return $budget;
    }

    public function index(Request $request, $account_id = null){
        $budget = $request->user()->findAccountOrFail($account_id)->budget;
        if(!isset($budget)){
            return CustomResponse::notFound();
        }
        return $budget;
    }

    public function destroy(Request $request, $account_id = null){
        $budget = $request->user()->findAccountOrFail($account_id)->budget();
        if(!isset($budget)){
            return CustomResponse::notFound();
        }
        $budget->delete();
    }
}
