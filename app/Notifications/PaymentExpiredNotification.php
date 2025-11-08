<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->order->booking;
        
        return (new MailMessage)
            ->subject('Waktu Pembayaran Habis')
            ->greeting('Halo ' . $notifiable->name)
            ->line('Waktu pembayaran untuk booking Anda telah habis.')
            ->line('**Detail Booking:**')
            ->line('- Lapangan: ' . $booking->field->name)
            ->line('- Tanggal: ' . $booking->booking_date->format('d F Y'))
            ->line('- Jam: ' . $booking->start_time . ' - ' . $booking->end_time)
            ->line('Slot booking telah dirilis dan tersedia untuk pengguna lain.')
            ->line('Silakan pesan ulang jika Anda masih ingin booking.')
            ->action('Booking Lagi', url('/fields'))
            ->line('Terima kasih atas pengertiannya.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total,
            'status' => 'expired',
            'message' => 'Waktu pembayaran expired untuk ' . $this->order->booking->field->name,
        ];
    }
}
