<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;

use App\Account;

use App\Custom\Fetch;
use App\Custom\Responses;

class AccountController extends Controller {

	public function childaccounts(Request $request, $account_id = null)
	{
		$accounts = Fetch::accountsWithParent($request->user_id, $account_id);
		if(!sizeof($accounts)){
			return Responses::noChildAccounts();
		}
		return Responses::json($accounts, 200);
	}

	public function index(Request $request)
	{
		return $this->childaccounts($request, null);
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

		Fetch::parentOrFail($request->user_id, $body['parent_id']);

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
		if($request->has('parent_id')){
			Fetch::parentOrFail($request->user_id, $request->input('parent_id'));
		}
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
