<?php

namespace App\Listeners;

use App\Events\PaymentSuccessful;
use App\Notifications\PaymentConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPaymentConfirmationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PaymentSuccessful $event): void
    {
        $order = $event->order;
        
        try {
            // Send notification to user
            $order->user->notify(new PaymentConfirmed($order));
            
            Log::info('Payment confirmation sent', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
