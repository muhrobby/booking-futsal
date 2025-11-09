<?php

namespace App\Console;

use App\Jobs\CancelExpiredBookings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run auto-cancel job every 5 minutes to check for expired bookings
        $schedule->job(new CancelExpiredBookings())
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->description('Cancel bookings that have expired payment timeout');

        // Optional: Also add a daily cleanup at midnight
        $schedule->job(new CancelExpiredBookings())
            ->dailyAt('00:00')
            ->withoutOverlapping()
            ->description('Daily cleanup of expired bookings');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
