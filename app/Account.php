<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $primaryKey = 'account_id';
    protected $fillable = [
        'parent_id', 'user_id', 'name',
        'balance', 'balance_limit', 'currency',
        'type', 'description'
    ];
    protected $hidden = [
        'user_id',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'user_id');
    }

    public function budget(){
        return $this->hasOne('App\Budget', 'account_id', 'account_id');
    }

    public function transactions(){
        return $this->hasMany('App\Transaction', 'account_id', 'account_id');
    }

    public function transaction($transaction_id){
        return $this->transactions()->findOrFail($transaction_id);
    }
}
