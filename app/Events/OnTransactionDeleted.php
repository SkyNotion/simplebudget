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

class OnTransactionDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $account;
    public $transaction;

    /**
     * Create a new event instance.
     *
     * @param Transaction $transaction
     * @return void
     */
    public function __construct()
    {
        $this->account = $transaction->account();
        $this->transaction = $transaction;
    }
}
