<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;

use App\Account;

class AccountController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$accounts = Account::where('user_id', '=', $request->user_id)->get();
		if(!sizeof($accounts)){
			return response(204);
		}
		return response()->json($accounts, 200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{	
		$body = $request->all();
		$rv = Validator::make($body, ['name' => 'required'],
			['name.required' => 'Account must have a name']
		);

		if($rv->fails()){
			return response()->json(['error' => $rv->errors()->first()], 400);
		}

		if(isset($body['parent_id'])){
			if(!sizeof(Account::whereRaw(
				'account_id = ? and user_id = ?', [$body['parent_id'], $request->user_id]
			)->first())){
				return response()->json(["error" => 'Parent account, does not exists'], 404);
			}
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

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Request $request, $account_id = null)
	{
		$account = Account::whereRaw('user_id = ? and account_id = ?', 
			[$request->user_id, $account_id])->first();
		if(!sizeof($account)){
			return response()->json(['error' => 'Account does not exist'], 404);
		}
		return response()->json($account, 200);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $account_id = null)
	{
		$account = Account::whereRaw('user_id = ? and account_id = ?', 
			[$request->user_id, $account_id])->first();
		if(!sizeof($account)){
			return response()->json(['error' => 'Account not found'], 404);
		}
		$account
		return response()->json($account, 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
