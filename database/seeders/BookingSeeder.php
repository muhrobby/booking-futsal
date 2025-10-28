<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Field;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $fields = Field::where('is_active', true)->get();
        $timeSlots = TimeSlot::all();

        if ($fields->isEmpty() || $timeSlots->isEmpty()) {
            $this->command->warn('Please run FieldSeeder and TimeSlotSeeder first!');
            return;
        }

        $this->command->info('Creating bookings...');

        // Create bookings for each user for the next 30 days
        foreach ($users as $user) {
            // Random number of bookings per day (0-2)
            $bookingsPerDay = rand(0, 2);
            
            for ($day = 0; $day < 30; $day++) {
                $bookingDate = Carbon::now()->addDays($day);
                
                // Random bookings for this day
                $dailyBookings = rand(0, $bookingsPerDay);
                
                for ($b = 0; $b < $dailyBookings; $b++) {
                    $field = $fields->random();
                    $timeSlot = $timeSlots->random();
                    
                    // Try to create booking, skip if slot already taken
                    try {
                        Booking::create([
                            'user_id' => $user->id,
                            'field_id' => $field->id,
                            'time_slot_id' => $timeSlot->id,
                            'booking_date' => $bookingDate->format('Y-m-d'),
                            'customer_name' => $user->name,
                            'customer_phone' => $user->phone,
                            'status' => $this->randomStatus($bookingDate),
                            'notes' => rand(0, 1) ? 'Booking via seeder' : null,
                        ]);
                    } catch (\Exception $e) {
                        // Skip if slot already taken (unique constraint)
                        continue;
                    }
                }
            }
        }

        $totalBookings = Booking::count();
        $this->command->info("Created {$totalBookings} bookings successfully!");
    }

    private function randomStatus($bookingDate)
    {
        // Past bookings
        if ($bookingDate->isPast()) {
            return collect(['confirmed', 'canceled'])->random();
        }
        
        // Future bookings
        return collect(['pending', 'confirmed'])->random();
    }
}
