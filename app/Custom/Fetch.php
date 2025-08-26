<?php namespace App\Custom;

use App\Account;

class Fetch{
	public static function account($user_id, $account_id){
		return Account::where('user_id', $user_id)
					  ->where('account_id', $account_id)
					  ->first();
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

	public static function budget($account_id){
		return Budget::where('account_id', $account_id)
					 ->first();
	}

}