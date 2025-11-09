<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CancelExpiredBookings implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Cancel bookings yang pending dan sudah expired
        $expiredBookings = Booking::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredBookings as $booking) {
            // Release lock jika masih active
            if ($booking->isLocked()) {
                $booking->releaseLock('auto_cancel_expired');
            }

            // Update status ke cancelled
            $booking->update([
                'status' => 'cancelled',
                'notes' => ($booking->notes ?? '') . "\n[Auto-cancelled: Payment timeout after 30 minutes]",
            ]);
        }

        if ($expiredBookings->count() > 0) {
            Log::info("Auto-cancelled {$expiredBookings->count()} expired bookings");
        }
    }
}
