<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessfulEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}
}
