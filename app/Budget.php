<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'budgets';
    protected $primaryKey = 'budget_id';
    protected $fillable = [
        'account_id', 'name', 'description',
        'budget', 'balance', 'entities'
    ];
    protected $hidden = [
        'account_id',
    ];
}
