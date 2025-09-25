<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Validator;
use App\Custom\Responses as CustomResponse;

use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function create(Request $request, $account_id = null, $transaction_id = null){
        $account = $request->user()->findAccountOrFail($account_id);
        $body = $request->except('account_id', 'balance');
        $rv = Validator::make($body,
            ['description' => 'string|nullable',
             'amount' => 'numeric|required_with:type|nullable',
             'type' => 'string|size:1|in:d,w|required_with:amount|nullable']
        );

        if($rv->fails()){
            return CustomResponse::badRequest($rv->errors()->first());
        }

        $args = [$body];
        if(isset($body['amount']) && isset($body['type'])){
            if(isset($transaction_id)){
                $transaction = $account->findTransactionOrFail($transaction_id);
                $account->balance += $transaction->type === 'd' ?
                                     -$transaction->amount : 
                                     $transaction->amount;
                $body['id'] = $transaction->id;
            }
            $account->balance += $body['type'] === 'd' ?
                                 $body['amount'] :
                                 -$body['amount'];
            $account->save();
            $account->refresh();
            $body['balance'] = $account->balance;
            $args = isset($transaction_id) ?
                    [['id' => $transaction_id], $body] : [$body];
        }

        return $account->transactions()->updateOrCreate(...$args);
    }

    public function index(Request $request, $account_id = null){
        $account = $request->user()->findAccountOrFail($account_id);
        $offset = $request->input('offset');

        $transactions = $account->transactions();

        if($request->has('search')){
            $search = "%{$request->input('search')}%";
            $transactions = $transactions->where('description', 'like', $search)
                                         ->orWhere('amount', 'like', $search)
                                         ->orWhere('type', 'like', $search)
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
        $request->user()->findAccountOrFail($account_id)
                ->findTransactionOrFail($transaction_id)
                ->delete();
    }
}
