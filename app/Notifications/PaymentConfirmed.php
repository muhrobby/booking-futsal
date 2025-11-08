<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification implements ShouldQueue
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
            ->subject('Pembayaran Berhasil - Booking Confirmed')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Pembayaran Anda telah berhasil dikonfirmasi.')
            ->line('**Detail Booking:**')
            ->line('- Lapangan: ' . $booking->field->name)
            ->line('- Tanggal: ' . $booking->booking_date->format('d F Y'))
            ->line('- Jam: ' . $booking->start_time . ' - ' . $booking->end_time)
            ->line('- Total: Rp ' . number_format($this->order->total, 0, ',', '.'))
            ->line('**Nomor Order:** ' . $this->order->order_number)
            ->action('Lihat Detail Booking', url('/bookings/' . $booking->id))
            ->line('Terima kasih telah menggunakan layanan kami!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total,
            'status' => 'paid',
            'message' => 'Pembayaran berhasil untuk booking ' . $this->order->booking->field->name,
        ];
    }
}
