<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Budget;

use App\Custom\Fetch;
use App\Custom\Responses;

class BudgetController extends Controller {

	public function index(Request $request, $account_id = null)
	{
		return Responses::json(Fetch::budgetOrFail($request->user_id, $account_id));
	}

	public function create(Request $request, $account_id = null)
	{
		$account = Fetch::accountOrFail($request->user_id, $account_id);
		$body = $request->all();
		if(!sizeof($body)){
			Responses::noRequestBody();
		}

		$body['account_id'] = $account_id;
		if(isset($body['entities'])){
			$budget_limit = 0.0;
			for($i = 0;$i < sizeof($body['entities']);$i++){
				$budget_limit += $body['entities'][$i]['amount'];
			}
			$body['entities'] = json_encode($body['entities']);
			$body['budget_limit'] = $budget_limit;
		}

		$budget = Budget::firstOrNew($body);
		$budget->touch();
		return Responses::json($budget->fresh(), 201);
	}

	public function destroy(Request $request, $account_id = null)
	{
		$budget = Fetch::budgetOrFail($request->user_id, $account_id);
		if($budget->delete()){
			return Responses::success();
		}
	}

}
