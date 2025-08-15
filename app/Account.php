<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model {
	protected $table = 'accounts';
	protected $primaryKey = 'account_id';
	protected $fillable = ['parent_id', 'user_id',
						   'name', 'balance', 'balance_limit',
						   'currency', 'type', 'description'];
}
