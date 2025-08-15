<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use DB;

use App\Transaction;

class TransactionController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request, $account_id = null)
	{
		$transactions = DB::table('transactions')
				->join('accounts', 'transactions.account_id', '=', 'accounts.account_id')
				->where('accounts.user_id', $request->user_id);
		if($request->has('search')){
			$transactions = $transactions
				->whereRaw('transactions.description like ? or transactions.description like ? or transactions.description like ? or transactions.description like ?', 
				array_fill(0, 4, '%'.$request->input('search').'%'));
		}elseif($request->has('date_range')){
			$transactions = $transactions
				->whereBetween('transactions.created_at', explode(";", $request->input('date_range')));
		}
		$transactions = $transactions->select(
					'transactions.transaction_id', 'transactions.description',
					'transactions.deposit', 'transactions.withdrawal','transactions.balance',
					'transactions.created_at', 'transactions.updated_at'
				)->get()->toArray();
		if(!sizeof($transactions)){
			return response()->json(['message' => 'No transactions found'], 204);
		}
		return response()->json($transactions, 200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		
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
	public function show($id)
	{
		//
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
	public function update($id)
	{
		//
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
