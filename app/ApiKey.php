<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model {
	protected $table = 'api_keys';
	protected $primaryKey = 'key_id';
	protected $fillable = ['user_id', 'name',  'api_key'];
}
