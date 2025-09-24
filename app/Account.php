<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $fillable = [
        'parent_id', 'user_id', 'name',
        'balance', 'balance_limit', 'currency',
        'type', 'description'
    ];
    protected $hidden = [
        'user_id',
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function budget(){
        return $this->hasOne('App\Budget');
    }

    public function transactions(){
        return $this->hasMany('App\Transaction');
    }

    public function findTransactionOrFail($transaction_id){
        return $this->transactions()->findOrFail($transaction_id);
    }
}
