<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'account_id', 'description', 'amount',
        'type', 'balance'
    ];
    protected $hidden = [
        'account_id',
    ];

    public function account(){
        return $this->belongsTo('App\Account');
    }
}
