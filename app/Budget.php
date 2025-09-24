<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'budgets';
    protected $fillable = [
        'account_id', 'name', 'description',
        'budget', 'balance', 'entities'
    ];
    protected $hidden = [
        'account_id',
    ];
}
