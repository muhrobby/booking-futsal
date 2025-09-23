<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TimeSlot::query()->delete();
        // 08:00 s/d 22:00 per jam
        for ($h = 8; $h < 22; $h++) {
            $start = sprintf('%02d:00:00', $h);
            $end   = sprintf('%02d:00:00', $h + 1);
            TimeSlot::create(['start_time' => $start, 'end_time' => $end, 'is_active' => true]);
        }
    }
}
