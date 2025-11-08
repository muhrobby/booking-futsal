<?php

namespace App\Listeners;

use App\Events\PaymentExpiredEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentExpiredNotification
{
    public function handle(PaymentExpiredEvent $event): void
    {
        $order = $event->order;
        $user = $order->user;
        $booking = $order->booking;

        try {
            Log::info('Sending payment expired notification', [
                'order_id' => $order->id,
                'user_email' => $user->email,
            ]);

            // Send email notification
            Mail::send('emails.payment-expired', [
                'order' => $order,
                'user' => $user,
                'booking' => $booking,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Waktu Pembayaran Berakhir - Booking Anda Dibatalkan');
            });

            Log::info('Payment expired notification sent', [
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment expired notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
