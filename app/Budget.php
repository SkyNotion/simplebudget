<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model {
	protected $table = 'budgets';
	protected $hidden = ['account_id'];
	protected $primaryKey = 'budget_id';
	protected $fillable = ['account_id', 'name', 'description', 'budget_limit',
						   'entities'];
}
