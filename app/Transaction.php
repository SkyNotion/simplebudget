<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
	protected $table = 'transactions';
	protected $hidden = ['account_id'];
	protected $primaryKey = 'transaction_id';
}
