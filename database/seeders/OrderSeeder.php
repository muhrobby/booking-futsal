<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Field;
use App\Models\Order;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('role', 'member')->first() ?? User::where('role', 'admin')->first();
        $field = Field::first();
        $timeSlot = TimeSlot::first();

        if (!$user || !$field || !$timeSlot) {
            return;
        }

        // Create a pending booking
        $pendingBooking = Booking::create([
            'field_id' => $field->id,
            'time_slot_id' => $timeSlot->id,
            'booking_date' => now()->addDays(2),
            'customer_name' => $user->name,
            'customer_phone' => $user->phone ?? '082123456789',
            'user_id' => $user->id,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(30),
        ]);

        // Create pending order
        Order::create([
            'user_id' => $user->id,
            'booking_id' => $pendingBooking->id,
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . uniqid(),
            'status' => 'pending',
            'subtotal' => $field->price_per_hour,
            'tax' => 0,
            'discount' => 0,
            'total' => $field->price_per_hour,
            'currency' => 'IDR',
            'payment_method' => 'xendit',
            'xendit_invoice_id' => 'inv_' . uniqid(),
            'payment_reference' => 'https://checkout-staging.xendit.co/web/inv_' . uniqid(),
        ]);

        // Create another pending booking (different time)
        $pendingBooking2 = Booking::create([
            'field_id' => $field->id,
            'time_slot_id' => TimeSlot::skip(1)->first()->id ?? $timeSlot->id,
            'booking_date' => now()->addDays(3),
            'customer_name' => $user->name,
            'customer_phone' => $user->phone ?? '082987654321',
            'user_id' => $user->id,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(25),
        ]);

        // Create processing order
        Order::create([
            'user_id' => $user->id,
            'booking_id' => $pendingBooking2->id,
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . uniqid(),
            'status' => 'processing',
            'subtotal' => $field->price_per_hour,
            'tax' => 0,
            'discount' => 0,
            'total' => $field->price_per_hour,
            'currency' => 'IDR',
            'payment_method' => 'xendit',
            'xendit_invoice_id' => 'inv_' . uniqid(),
            'payment_reference' => 'https://checkout-staging.xendit.co/web/inv_' . uniqid(),
        ]);

        // Create failed order
        $failedBooking = Booking::create([
            'field_id' => $field->id,
            'time_slot_id' => TimeSlot::skip(2)->first()->id ?? $timeSlot->id,
            'booking_date' => now()->addDays(4),
            'customer_name' => $user->name,
            'customer_phone' => $user->phone ?? '082555666777',
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        Order::create([
            'user_id' => $user->id,
            'booking_id' => $failedBooking->id,
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . uniqid(),
            'status' => 'failed',
            'subtotal' => $field->price_per_hour,
            'tax' => 0,
            'discount' => 0,
            'total' => $field->price_per_hour,
            'currency' => 'IDR',
            'payment_method' => 'xendit',
            'xendit_invoice_id' => 'inv_' . uniqid(),
        ]);
    }
}
