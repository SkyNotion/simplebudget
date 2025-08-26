<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;

use App\Account;

use App\Custom\Fetch;
use App\Custom\Responses;

class AccountController extends Controller {

	public function get_children($user_id, $parent_id)
	{
		$accounts = Fetch::accountsWithParent($user_id, $parent_id);
		if(!sizeof($accounts)){
			return array();
		}
		foreach($accounts as &$account){
			$account['children'] = $this->get_children($user_id, $account['account_id']);
		}
		return $accounts;
	}

	public function childaccounts(Request $request, $account_id = null)
	{
		$accounts = Fetch::accountsWithParent($request->user_id, $account_id);
		if(!sizeof($accounts)){
			return Responses::noChildAccounts();
		}
		foreach($accounts as &$account){
			$account['children'] = $this->get_children($request->user_id, $account['account_id']);
		}
		return Responses::json($accounts, 200);
	}

	public function index(Request $request)
	{
		$accounts = Fetch::accountsWithParent($request->user_id, null);
		if(!sizeof($accounts)){
			return Responses::noChildAccounts();
		}
		foreach($accounts as &$account){
			$account['children'] = $this->get_children($request->user_id, $account['account_id']);
		}
		return Responses::json($accounts, 200);
	}

	public function create(Request $request)
	{
		$body = $request->all();
		$rv = Validator::make($body, ['name' => 'required'],
			['name.required' => 'Account must have a name']
		);

		if($rv->fails()){
			return Responses::error($rv->errors()->first(), 400);
		}

		if(isset($body['parent_id'])){
			if(Fetch::account($request->user_id, $body['parent_id']) == null){
				return Responses::noParent();
			}
		}

		$body['user_id'] = $request->user_id;
		if(isset($body['opening_balance'])){
			$body['balance'] = $body['opening_balance'];
		}

	    return Responses::json(Account::create($body)->fresh(), 201);
	}

	public function show(Request $request, $account_id = null)
	{
		$account = Fetch::accountOrFail($request->user_id, $account_id);
		return Responses::json($account);
	}

	public function update(Request $request, $account_id = null)
	{
		$account = Fetch::accountOrFail($request->user_id, $account_id);
		$account->update($request->except("user_id"));
		return Responses::json($account->fresh());
	}

	public function destroy(Request $request, $account_id = null)
	{
		$account = Fetch::accountOrFail($request->user_id, $account_id);
		if($account->delete()){
			return Responses::success();
		}
	}

}
