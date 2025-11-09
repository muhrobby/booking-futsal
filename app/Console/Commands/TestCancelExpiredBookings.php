<?php

namespace App\Console\Commands;

use App\Jobs\CancelExpiredBookings;
use Illuminate\Console\Command;

class TestCancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cancel-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test cancel expired bookings job manually';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Running cancel expired bookings job...');
        
        dispatch(new CancelExpiredBookings());
        
        $this->info('Job dispatched successfully!');
        return 0;
    }
}
