<?php namespace App\Custom;

use App\Account;
use App\Budget;

use App\Exceptions\PlainHttpException;

class Fetch{
	public static function account($user_id, $account_id){
		return Account::where('user_id', $user_id)
					  ->where('account_id', $account_id)
					  ->first();
	}

	public static function accountOrFail($user_id, $account_id){
		$account = self::account($user_id, $account_id);
		if($account == null){
			throw new PlainHttpException('Account does not exist', 404);
		}
		return $account;
	}

	public static function accountsWithParent($user_id, $parent_id){
		$accounts = Account::where('user_id', $user_id);
		if($parent_id == null){
			$accounts = $accounts->whereNull('parent_id', $parent_id);
		}else{
			$accounts = $accounts->where('parent_id', $parent_id);
		}
		return $accounts->get();
	}

	public static function budget($user_id, $account_id){
		return Budget::join('accounts', 'budgets.account_id', '=', 'accounts.account_id')
					 ->where('accounts.user_id', $user_id)
					 ->where('budgets.account_id', $account_id)
					 ->select('budgets.budget_id', 'budgets.name', 'budgets.description',
					 		  'budgets.budget_limit', 'budgets.created_at', 'budgets.updated_at',
					 		  'budgets.entities')
					 ->first();
	}

	public static function budgetOrFail($user_id, $account_id){
		$budget = self::budget($user_id, $account_id);
		if($budget == null){
			throw new PlainHttpException('Account or Budget does not exist', 404);
		}
		return $budget;
	}

}