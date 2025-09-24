<?php

namespace App\Listeners;

use App\Events\OnTransactionSaved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Notifications\BudgetAlert;

class OnTransactionSavedListener
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
     * @param  OnTransactionSaved  $event
     * @return void
     */
    public function handle(OnTransactionSaved $event)
    {
        $transaction = $event->transaction;
        $budget = $account->budget();
        if(isset($budget)){
            if(isset($transaction->desposit)){
                $budget->balance += $transaction->desposit;
            }elseif(isset($transaction->withdrawal)){
                $budget->balance -= $transaction->withdrawal;
            }
            $budget->save()->refresh();

            $usage = ($budget->balance/$budget->budget)*100;
            $alert = [
                'balance' => $budget->balance,
                'used' => $usage,
                'budget' => $budget->budget
            ];

            if($usage > 70 && $usage <= 100){
                $alert['message'] = "You have used $usage% of your budget";
            }elseif($usage > 100) {
                $alert['message'] = "You have passed your budget by ".($budget->balance-$budget->budget).$account->currency;
            }elseif($usage < 0){
                $alert['message'] = "Your budget is negative, ".$budget->balance;
            }

            if(isset($alert['message'])){
                $event->user->notify(new BudgetAlert($alert));
            }
        }
    }
}
