<?php

namespace App\Observers;

use App\Transaction;

class OnTransactionObserver {
    public function saved(Transaction $transaction){
        $account = $transaction->account;
        $budget = $account->budget;
        $user = $account->user;

        if(isset($budget)){
            if(isset($transaction->type)){
                $budget->balance += $transaction->type === 'd' ?
                                    $transaction->desposit :
                                    -$transaction->withdrawal;
                $budget->save();
                $budget->refresh();
            }

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
                $user->notify(new BudgetAlert($alert));
            }
        }
    }

    public function deleted(Transaction $transaction){
        $account = $transaction->account;
        $budget = $account->budget;

        if(isset($transaction->type)){
            if($transaction->type === 'd'){
                $account->balance -= $transaction->amount;
                if(isset($budget)){
                    $budget->balance -= $transaction->amount;
                    $budget->save();
                }
            }else{
                $account->balance += $transaction->amount;
                if(isset($budget)){
                    $budget->balance += $transaction->amount;
                    $budget->save();
                }
            }
            $account->save();
        }
    }
}