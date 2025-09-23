<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Custom\Responses as CustomResponse;

class TransactionController extends Controller
{
    public function create(Request $request, $account_id = null){
        $account = $request->user()->account($account_id);
        $body = $request->only('description', 'deposit', 'withdrawal');
        $rv = Validator::make($body,
            ['description' => 'string',
             'deposit' => 'numeric',
             'withdrawal' => 'numeric']
        );

        if($rv->fails()){
            return CustomResponse::badRequest($rv->errors()->first());
        }

        if($body->has('desposit')){
            $account->balance += $body['deposit'];
        }elseif($body->has('withdrawal')){
            $account->balance -= $body['withdrawal'];
        }
        $account->save()->refresh();
        $body['balance'] = $account->balance;

        return $account->transactions()->updateOrCreate($body);
    }

    public function index(Request $request, $account_id = null){
        $account = $request->user()->account($account_id);
        $offset = $request->input('offset');

        $transactions = $account->transactions();

        if($request->has('search')){
            $search = "%{$request->input('search')}%"
            $transactions = $transactions->where('description', 'like', $search)
                                         ->orWhere('deposit', 'like', $search)
                                         ->orWhere('withdrawal', 'like', $search)
                                         ->orWhere('balance', 'like', $search);
        }

        if($request->has('range')){
            $transactions = $transactions->whereBetween('updated_at', 
                                explode(";", $request->input('range')));
        }

        $transactions = $transactions->skip($offset)
                                     ->take(20)->get();

        if(!isset($transactions)){
            return CustomResponse::noContent();
        }

        return $transactions;
    }

    public function destroy(Request $request, $account_id = null, $transaction_id = null){
        $request->user()->account($account_id)
                ->transaction($transaction_id)
                ->delete();
    }
}
