<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;

use App\Transaction;
use App\Account;
use App\Budget; 

use App\Http\Controllers\NotificationController;

use App\Custom\Fetch;
use App\Custom\Responses;

class TransactionController extends Controller {

	public function index(Request $request, $account_id = null)
	{
		$transactions = Transaction::join('accounts', 'transactions.account_id', '=', 'accounts.account_id')
				->where('accounts.user_id', $request->user_id)
				->where('transactions.account_id', $account_id);
		if($request->has('search')){
			$search_query = '%'.(string)$request->input('search').'%';
			$transactions = $transactions->where('transactions.description', 'like', $search_query)
				->orWhere('transactions.deposit', 'like', $search_query)
				->orWhere('transactions.withdrawal', 'like', $search_query)
				->orWhere('transactions.balance', 'like', $search_query);
		}elseif($request->has('date_range')){
			$transactions = $transactions
				->whereBetween('transactions.created_at', explode(";", $request->input('date_range')));
		}
		$transactions = $transactions->select(
					'transactions.transaction_id', 'transactions.description',
					'transactions.deposit', 'transactions.withdrawal','transactions.balance',
					'transactions.created_at', 'transactions.updated_at')
					->get();
		if(!sizeof($transactions)){
			return Responses::message('No transactions found', 204);
		}
		return Responses::json($transactions, 200);
	}

	public function create(Request $request, $account_id = null)
	{
		$body = $request->all();
		if(!(isset($body['deposit']) ^ isset($body['withdrawal']))){
			return Responses::error('Cannot be both a deposit and withdrawal', 400);
		}

		$account = Fetch::accountOrFail($request->user_id, $account_id);
		$budget = Fetch::budget($request->user_id, $account_id);

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
		$transaction->fresh();

		if(sizeof($budget)){
			$message = null;
			$total_deposits = Transaction::where('account_id', $account_id)
			 							 ->where('created_at', '>=', $budget->updated_at->toDateTimeString())
										 ->whereNotNull("deposit")
										 ->sum("deposit");
			$total_withdrawal = Transaction::where('account_id', $account_id)
			 							   ->where('created_at', '>=', $budget->updated_at->toDateTimeString())
										   ->whereNotNull("withdrawal")
										   ->sum("withdrawal");
			$budget_balance = $total_withdrawal - $total_deposits;
			$percentage = ($budget_balance/$budget->budget_limit)*100;
			if($percentage > 70 && $percentage <= 100){
				$message = "You have used $percentage% of your budget";
			}elseif ($percentage > 100) {
				$message = "You have passed your budget by ".(string)($budget_balance-$budget->budget_limit).$account->currency;
			}
			if(!is_null($message)){
				NotificationController::create([
					'user_id' => $request->user_id,
					'source' => 'budget',
					'source_id' => $account_id,
					'content' => $message
				]);
			}
		}

		if(isset($body['deposit'])){
			NotificationController::create([
				'user_id' => $request->user_id,
				'source' => 'transaction',
				'source_id' => $transaction->transaction_id,
				'content' => json_encode($transaction->toArray())
			]);
		}

		return Responses::json($transaction);
	}

	public function destroy(Request $request, $account_id = null, $transaction_id = null)
	{
		if(!Transaction::join('accounts', 'transactions.account_id', '=', 'accounts.account_id')
				->where('accounts.user_id', $request->user_id)
				->where('transactions.account_id', $account_id)
				->where('transactions.transaction_id', $transaction_id)->delete()){
			return Responses::noTransaction();
		}
		return Responses::success();
	}

}
