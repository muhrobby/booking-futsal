<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $errorMessage = ''
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->order->booking;
        
        return (new MailMessage)
            ->subject('Pembayaran Gagal - Silakan Coba Lagi')
            ->greeting('Halo ' . $notifiable->name)
            ->line('Maaf, pembayaran Anda tidak berhasil diproses.')
            ->line('**Detail Booking:**')
            ->line('- Lapangan: ' . $booking->field->name)
            ->line('- Tanggal: ' . $booking->booking_date->format('d F Y'))
            ->line('- Jam: ' . $booking->start_time . ' - ' . $booking->end_time)
            ->line('- Total: Rp ' . number_format($this->order->total, 0, ',', '.'))
            ->line('**Alasan:** ' . ($this->errorMessage ?: 'Pembayaran tidak berhasil'))
            ->action('Coba Bayar Lagi', url('/orders/' . $this->order->id . '/checkout'))
            ->line('Jika ada pertanyaan, silakan hubungi customer service kami.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total,
            'status' => 'failed',
            'error' => $this->errorMessage,
            'message' => 'Pembayaran gagal untuk booking ' . $this->order->booking->field->name,
        ];
    }
}
