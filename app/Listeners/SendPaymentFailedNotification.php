<?php

namespace App\Listeners;

use App\Events\PaymentFailedEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentFailedNotification
{
    public function handle(PaymentFailedEvent $event): void
    {
        $order = $event->order;
        $user = $order->user;
        $booking = $order->booking;
        $errorMessage = $event->errorMessage;

        try {
            Log::info('Sending payment failed notification', [
                'order_id' => $order->id,
                'user_email' => $user->email,
            ]);

            // Send email notification
            Mail::send('emails.payment-failed', [
                'order' => $order,
                'user' => $user,
                'booking' => $booking,
                'errorMessage' => $errorMessage,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Pembayaran Gagal - Silakan Coba Lagi');
            });

            Log::info('Payment failed notification sent', [
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment failed notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
