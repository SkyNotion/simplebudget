<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;

use App\Account;
use App\Exceptions\PlainHttpException;

class AccountController extends Controller {

	public function get_account($user_id, $account_id, $message = 'Account does not exist'){
		$account = Account::where('user_id', $user_id)
						  ->where('account_id', $account_id)
						  ->first();
		if(!sizeof($account)){
			throw new PlainHttpException($message, 404);
		}
		return $account;
	}

	public function get_children($user_id, $parent_id){
		$accounts = Account::where('user_id', $user_id)
						   ->where('parent_id', $parent_id)
						   ->get()->toArray();
		if(!sizeof($accounts)){
			return array();
		}
		foreach($accounts as &$account){
			$account['children'] = $this->get_children($user_id, $account['account_id']);
		}
		return $accounts;
	}

	public function index(Request $request)
	{
		$accounts = Account::where('user_id', $request->user_id)
						   ->whereNull('parent_id')->get()->toArray();
		if(!sizeof($accounts)){
			return response('No accounts found', 204);
		}
		foreach($accounts as &$account){
			$account['children'] = $this->get_children($request->user_id, $account['account_id']);
		}
		return response()->json($accounts, 200);
	}

	public function childaccounts(Request $request, $account_id = null){
		$accounts = Account::where('user_id', $request->user_id)
						   ->where('parent_id', $account_id)
						   ->get()->toArray();
		if(!sizeof($accounts)){
			throw new PlainHttpException($message, 404);
		}
		foreach($accounts as &$account){
			$account['children'] = $this->get_children($request->user_id, $account['account_id']);
		}
		return response()->json($accounts, 200);
	}

	public function create(Request $request)
	{	
		$body = $request->all();
		$rv = Validator::make($body, ['name' => 'required'],
			['name.required' => 'Account must have a name']
		);

		if($rv->fails()){
			throw new PlainHttpException($rv->errors()->first(), 400);
		}

		if(isset($body['parent_id'])){
			$this->get_account($request->user_id, $body['parent_id'], 'Parent account, does not exists');
		}else{$body['parent_id'] = null;}

		$model = [
			'parent_id' => $body['parent_id'],
			'user_id' => $request->user_id,
			'name' => $body['name']
		];

		if(isset($body['opening_balance'])){$model = array_merge($model, 
			['balance' => $body['opening_balance']]);}

		if(isset($body['balance_limit'])){$model = array_merge($model, 
			['balance_limit' => $body['balance_limit']]);}

		if(isset($body['currency'])){$model = array_merge($model, 
			['currency' => $body['currency']]);}

		if(isset($body['type'])){$model = array_merge($model, 
			['type' => $body['type']]);}

		if(isset($body['description'])){$model = array_merge($model, 
			['description' => $body['description']]);}

	    return response()->json(Account::create($model)->fresh()->attributesToArray(), 201);
	}

	public function show(Request $request, $account_id = null)
	{
		return response()->json($this->get_account($request->user_id, $account_id), 200);
	}

	public function update(Request $request, $account_id = null)
	{
		$account = $this->get_account($request->user_id, $account_id);
		$account->update($request->except("user_id"));
		return response()->json($account->fresh(), 200);
	}

	public function destroy(Request $request, $account_id = null)
	{
		$this->get_account($request->user_id, $account_id)->delete();
		return response('Successful', 200);
	}

}
