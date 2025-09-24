<?php

namespace App;

use App\Events\OnTransactionSaved;
use App\Events\OnTransactionDeleted;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'account_id', 'description', 'deposit',
        'withdrawal', 'balance'
    ];
    protected $hidden = [
        'account_id',
    ];

    public function account(){
        return $this->belongsTo('App\Account');
    }

    public static function boot(){
        parent::boot();

        static::saved(function($transaction){
            event(new OnTransactionSaved($transaction));
        });

        static::deleted(function($transaction){
            event(new OnTransactionDeleted($transaction));
        });
    }
}
