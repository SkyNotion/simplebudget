<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Redis;

use App\Notification;

use App\Custom\Responses;

class NotificationController extends Controller {

	public function index(Request $request)
	{
		if($request->has('all')){
			if($request->input('all') != 'true'){
				return Responses::json(
					['error' => 'Invalid request, \'all\' cannot be set to \''.$request->input('all').'\'']
				, 400);
			}
			return Responses::json(Notification::where('user_id', $request->user_id)->get());
		}

		$notification_item = Redis::brpop(['notification:'.$request->user_id], 28);
		if(!$notification_item){
			return Responses::noContent();
		}

		return Responses::json(json_decode($notification_item[1]));
	}

	public static function create($notification_item){
		Redis::lpush('notification:'.$notification_item['user_id'], 
			json_encode(Notification::create($notification_item)->toArray()));
	}
}
