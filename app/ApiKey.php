<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model {
	protected $table = 'api_keys';
	public $incrementing = false;
	protected $fillable = ['user_id', 'name',  'api_key'];
}
