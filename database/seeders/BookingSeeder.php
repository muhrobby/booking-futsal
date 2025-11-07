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
        $members = User::where('role', 'member')->get();
        $fields = Field::all();
        $timeSlots = TimeSlot::all();

        if ($members->isEmpty() || $fields->isEmpty() || $timeSlots->isEmpty()) {
            $this->command->warn('⚠️  Skipping BookingSeeder: Missing required data (members, fields, or time slots)');
            return;
        }

        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        $bookingCount = 0;

        // Generate bookings for next 30 days with varied daily amounts
        for ($day = 1; $day <= 30; $day++) {
            $bookingDate = Carbon::now()->addDays($day);

            // Generate different number of bookings per day (2-8 bookings)
            $bookingsPerDay = rand(2, 8);

            for ($i = 0; $i < $bookingsPerDay; $i++) {
                // Pick random member and field
                $member = $members->random();
                $field = $fields->random();
                $timeSlot = $timeSlots->random();

                // Random status with more confirmed bookings (70% confirmed)
                $status = rand(1, 100) <= 70 ? 'confirmed' : $statuses[rand(0, 3)];

                try {
                    Booking::create([
                        'user_id' => $member->id,
                        'field_id' => $field->id,
                        'time_slot_id' => $timeSlot->id,
                        'booking_date' => $bookingDate->toDateString(),
                        'customer_name' => $member->name,
                        'customer_phone' => $member->phone,
                        'status' => $status,
                        'notes' => fake()->sentence(),
                        'created_at' => $bookingDate->subDays(rand(1, 7)),
                        'updated_at' => now(),
                    ]);
                    $bookingCount++;
                } catch (\Exception $e) {
                    // Skip if constraint violation
                    continue;
                }
            }
        }

        $this->command->info('✅ ' . $bookingCount . ' bookings created for 30 days with varied daily amounts!');
    }
}
