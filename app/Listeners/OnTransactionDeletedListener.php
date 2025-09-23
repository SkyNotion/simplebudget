<?php

namespace App\Listeners;

use App\Events\OnTransactionDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Notifications\BudgetAlert;

class OnTransactionDeletedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OnTransactionDeleted  $event
     * @return void
     */
    public function handle(OnTransactionDeleted $event)
    {
        $transaction = $event->transaction;
        $account = $event->account;
        $budget = $account->budget();

        if(isset($transaction->desposit)){
            $account->balance -= $transaction->desposit;
            if(isset($budget)){
                $budget->balance -= $transaction->desposit;
            }
        }elseif(isset($transaction->withdrawal)){
            $account->balance += $transaction->withdrawal;
            if(isset($budget)){
                $budget->balance += $transaction->withdrawal;
            }
        }
        $account->save();
        if(isset($budget)){
            $budget->save();
        }
    }
}
