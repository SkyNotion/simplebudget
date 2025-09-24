<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Transaction;

class OnTransactionSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $account;
    public $user;
    public $transaction;

    /**
     * Create a new event instance.
     *
     * @param Transaction $transaction
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->account = $transaction->account();
        $this->user = $this->account->user();
        $this->transaction = $transaction;
    }
}
