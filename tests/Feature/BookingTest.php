<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Field;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_booking(): void
    {
        $user = User::factory()->create([
            'role' => 'member',
            'phone' => '0811111111',
        ]);

        $field = Field::create([
            'name' => 'Lapangan Test',
            'description' => 'Test',
            'price_per_hour' => 100000,
            'is_active' => true,
        ]);

        $slot = TimeSlot::create([
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'is_active' => true,
        ]);

        $bookingDate = now()->addDay()->toDateString();

        $response = $this->actingAs($user)->post(route('bookings.store'), [
            'field_id' => $field->id,
            'time_slot_id' => $slot->id,
            'booking_date' => $bookingDate,
            'customer_name' => 'Member Test',
            'customer_phone' => '08123456789',
            'notes' => 'Catatan uji',
        ]);

        $response->assertRedirect(route('bookings.my', ['phone' => '08123456789']));

        $this->assertDatabaseHas('bookings', [
            'field_id' => $field->id,
            'time_slot_id' => $slot->id,
            'customer_phone' => '08123456789',
            'status' => 'pending',
        ]);

        $this->assertTrue(
            Booking::query()
                ->where('field_id', $field->id)
                ->where('time_slot_id', $slot->id)
                ->whereDate('booking_date', $bookingDate)
                ->exists()
        );
    }

    public function test_double_booking_is_rejected(): void
    {
        $user = User::factory()->create([
            'role' => 'member',
            'phone' => '0811111111',
        ]);

        $field = Field::create([
            'name' => 'Lapangan Test',
            'description' => 'Test',
            'price_per_hour' => 100000,
            'is_active' => true,
        ]);

        $slot = TimeSlot::create([
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'is_active' => true,
        ]);

        $bookingDate = now()->addDay()->toDateString();

        Booking::create([
            'field_id' => $field->id,
            'time_slot_id' => $slot->id,
            'booking_date' => $bookingDate,
            'customer_name' => 'Existing',
            'customer_phone' => '08100000000',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->post(route('bookings.store'), [
            'field_id' => $field->id,
            'time_slot_id' => $slot->id,
            'booking_date' => $bookingDate,
            'customer_name' => 'Member Test',
            'customer_phone' => '08123456789',
        ]);

        $response->assertSessionHasErrors('time_slot_id');
    }

    public function test_my_bookings_page_displays_user_bookings(): void
    {
        $user = User::factory()->create([
            'role' => 'member',
            'phone' => '0811111111',
        ]);

        $field = Field::create([
            'name' => 'Lapangan Test',
            'description' => 'Test',
            'price_per_hour' => 100000,
            'is_active' => true,
        ]);

        $slot = TimeSlot::create([
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'is_active' => true,
        ]);

        Booking::create([
            'field_id' => $field->id,
            'time_slot_id' => $slot->id,
            'booking_date' => now()->addDay()->toDateString(),
            'customer_name' => 'Member Test',
            'customer_phone' => '08123456789',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('bookings.my', ['phone' => '08123456789']));

        $response->assertStatus(200);
        $response->assertSee('Member Test');
        $response->assertSee('Lapangan Test');
    }

    public function test_canceled_booking_slot_can_be_reused(): void
    {
        $user = User::factory()->create([
            'role' => 'member',
            'phone' => '0811111111',
        ]);

        $field = Field::create([
            'name' => 'Lapangan Test',
            'description' => 'Test',
            'price_per_hour' => 100000,
            'is_active' => true,
        ]);

        $slot = TimeSlot::create([
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'is_active' => true,
        ]);

        $bookingDate = now()->addDay()->toDateString();

        $canceledBooking = Booking::create([
            'field_id' => $field->id,
            'time_slot_id' => $slot->id,
            'booking_date' => $bookingDate,
            'customer_name' => 'Old Customer',
            'customer_phone' => '08100000000',
            'status' => 'canceled',
        ]);

        $response = $this->actingAs($user)->post(route('bookings.store'), [
            'field_id' => $field->id,
            'time_slot_id' => $slot->id,
            'booking_date' => $bookingDate,
            'customer_name' => 'Member Test',
            'customer_phone' => '08123456789',
        ]);

        $response->assertRedirect(route('bookings.my', ['phone' => '08123456789']));

        $this->assertDatabaseHas('bookings', [
            'id' => $canceledBooking->id,
            'customer_name' => 'Member Test',
            'customer_phone' => '08123456789',
            'status' => 'pending',
        ]);
    }
}
