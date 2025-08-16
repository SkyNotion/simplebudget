<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
	protected $table = 'notifications';
	protected $primaryKey = 'notification_id';
	protected $fillable = ['notification_id', 'user_id', 'source',
						   'source_id', 'content'];
}
