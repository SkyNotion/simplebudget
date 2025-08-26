<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Budget;
use App\Account;

class BudgetController extends Controller {

	public function index(Request $request, $account_id = null)
	{
		$account = Account::where('user_id', $request->user_id)
						  ->where('account_id', $account_id)
						  ->first();
		if(!sizeof($account)){
			return response()->json(['error' => 'Account does not exist'], 404);
		}
		$budget = Budget::where('account_id', $account_id)->first();

		if($budget == null){
			return response()->json([], 200);
		}
		return response()->json($budget->toArray(), 200);
	}

	public function create(Request $request, $account_id = null)
	{
		$account = Account::where('user_id', $request->user_id)
						  ->where('account_id', $account_id)
						  ->first();
		if(!sizeof($account)){
			return response()->json(['error' => 'Account does not exist'], 404);
		}

		$body = $request->all();
		if(!sizeof($body)){
			return response()->json(['error' => 'Request body cannot be empty'], 400);
		}

		$budget = Budget::firstOrNew(['account_id' => $account_id]);

		if(isset($body['name'])){
			$budget->name = $body['name'];
		}
		if(isset($body['description'])){
			$budget->description = $body['description'];
		}
		if(isset($body['entities'])){
			$budget_limit = 0.0;
			for($i = 0;$i < sizeof($body['entities']);$i++){
				$budget_limit += $body['entities'][$i]['amount'];
			}
			$budget->entities = json_encode($body['entities']);
			$budget->budget_limit = $budget_limit;
		}	
		$budget->save();
		$budget->touch();
		return response()->json($budget->fresh()->toArray(), 201);
	}

	public function destroy(Request $request, $account_id = null)
	{
		$account = Account::where('user_id', $request->user_id)
						  ->where('account_id', $account_id)
						  ->first();
		if(!sizeof($account)){
			return response()->json(['error' => 'Account does not exist'], 404);
		}
		Budget::where('account_id', $account_id)->delete();
		return response()->json(['message' => 'Successful'], 200);
	}

}
