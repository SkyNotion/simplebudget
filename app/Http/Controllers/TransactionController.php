<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use DB;

use App\Transaction;
use App\Account;

class TransactionController extends Controller {

	public function index(Request $request, $account_id = null)
	{
		$transactions = DB::table('transactions')
				->join('accounts', 'transactions.account_id', '=', 'accounts.account_id')
				->where('accounts.user_id', $request->user_id)
				->where('transactions.account_id', $account_id);
		if($request->has('search')){
			$transactions = $transactions
				->whereRaw('transactions.description like ? or transactions.deposit like ? or transactions.withdrawal like ? or transactions.balance like ?', 
				array_fill(0, 4, '%'.(string)$request->input('search').'%'));
		}elseif($request->has('date_range')){
			$transactions = $transactions
				->whereBetween('transactions.created_at', explode(";", $request->input('date_range')));
		}
		$transactions = $transactions->select(
					'transactions.transaction_id', 'transactions.description',
					'transactions.deposit', 'transactions.withdrawal','transactions.balance',
					'transactions.created_at', 'transactions.updated_at'
				)->get();
		if(!sizeof($transactions)){
			return response()->json(['message' => 'No transactions found'], 204);
		}
		return response()->json($transactions, 200);
	}

	public function create(Request $request, $account_id = null)
	{
		$body = $request->all();
		if(!(isset($body['deposit']) ^ isset($body['withdrawal']))){
			return response()->json(['error' => 'Cannot be both a deposit and withdrawal'], 400);
		}

		$account = Account::whereRaw('user_id = ? and account_id = ?',
					[$request->user_id, $account_id])->first();
		if(!sizeof($account)){
			return response()->json(['error' => 'Account does not exist'], 404);
		}

		$transaction = new Transaction;
		$transaction->account_id = $account_id;

		if(isset($body['description'])){
			$transaction->description = $body['description'];
		}
		if(isset($body['deposit'])){
			$transaction->deposit = $body['deposit'];
			$account->balance = $account->balance + $body['deposit'];
			$account->save();
			$account = $account->fresh();
			$transaction->balance = $account->balance;
		}
		if(isset($body['withdrawal'])){
			$transaction->withdrawal = $body['withdrawal'];
			$account->balance = $account->balance - $body['withdrawal'];
			$account->save();
			$account = $account->fresh();
			$transaction->balance = $account->balance;
		}

		$transaction->save();

		return response()->json($transaction->fresh()->toArray(), 200);
	}

	public function destroy(Request $request, $account_id = null, $transaction_id = null)
	{
		if(!DB::table('transactions')
				->join('accounts', 'transactions.account_id', '=', 'accounts.account_id')
				->where('accounts.user_id', $request->user_id)
				->where('transactions.account_id', $account_id)
				->where('transactions.transaction_id', $transaction_id)->delete()){
			return response()->json(['error' => 'Account or transaction does not exist'], 404);
		}
		return response()->json(['message' => 'Successful'], 200);
	}

}
