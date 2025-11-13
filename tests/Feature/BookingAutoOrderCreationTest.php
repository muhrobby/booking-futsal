<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Field;
use App\Models\Order;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingAutoOrderCreationTest extends TestCase
{
    use RefreshDatabase;

    private User $member;
    private Field $field;
    private TimeSlot $timeSlot;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->member = User::factory()->create(['role' => 'member']);
        $this->field = Field::create([
            'name' => 'Test Field',
            'location' => 'Test Location',
            'price_per_hour' => 150000,
        ]);
        $this->timeSlot = TimeSlot::create([
            'start_time' => '08:00',
            'end_time' => '09:00',
        ]);
    }

    /**
     * Test that order is automatically created when booking is created
     */
    public function test_order_auto_created_when_booking_created(): void
    {
        // Act: Create booking
        $response = $this->actingAs($this->member)->post('/bookings', [
            'field_id' => $this->field->id,
            'time_slot_id' => $this->timeSlot->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'customer_name' => 'Test Customer',
            'customer_phone' => '08123456789',
        ]);

        // Assert: Check booking was created
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->member->id,
            'field_id' => $this->field->id,
        ]);

        // Assert: Check order was auto-created
        $booking = Booking::where('user_id', $this->member->id)->first();
        $this->assertNotNull($booking);

        $order = Order::where('booking_id', $booking->id)->first();
        $this->assertNotNull($order, 'Order should be auto-created with booking');
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(150000, $order->total);

        // Assert: Check redirect to order creation
        $response->assertRedirect(route('orders.create', $booking));
    }

    /**
     * Test that multiple bookings get separate orders
     */
    public function test_multiple_bookings_get_separate_orders(): void
    {
        // Create additional time slots
        $timeSlot2 = TimeSlot::create([
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        // Create first booking
        $this->actingAs($this->member)->post('/bookings', [
            'field_id' => $this->field->id,
            'time_slot_id' => $this->timeSlot->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'customer_name' => 'Test Customer 1',
            'customer_phone' => '08123456789',
        ]);

        // Create second booking
        $this->actingAs($this->member)->post('/bookings', [
            'field_id' => $this->field->id,
            'time_slot_id' => $timeSlot2->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'customer_name' => 'Test Customer 2',
            'customer_phone' => '08987654321',
        ]);

        // Assert: Check we have 2 bookings and 2 orders
        $this->assertCount(2, Booking::where('user_id', $this->member->id)->get());
        $this->assertCount(2, Order::whereIn('booking_id', Booking::where('user_id', $this->member->id)->pluck('id'))->get());
    }

    /**
     * Test that checkout page gets existing order
     */
    public function test_checkout_page_gets_existing_auto_created_order(): void
    {
        // Create booking (which auto-creates order)
        $this->actingAs($this->member)->post('/bookings', [
            'field_id' => $this->field->id,
            'time_slot_id' => $this->timeSlot->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'customer_name' => 'Test Customer',
            'customer_phone' => '08123456789',
        ]);

        $booking = Booking::where('user_id', $this->member->id)->first();
        $order = $booking->orders()->first();

        // Act: Visit checkout page
        $response = $this->actingAs($this->member)->get(route('orders.create', $booking));

        // Assert: Page loads successfully
        $response->assertStatus(200);
        $response->assertViewHas('order');
        $response->assertViewHas('booking');

        // Assert: Order shown is the auto-created one
        $this->assertEquals($order->id, $response->viewData('order')->id);
    }

    /**
     * Test that booking lock is created with auto-created order
     */
    public function test_booking_lock_created_with_auto_order(): void
    {
        // Create booking
        $this->actingAs($this->member)->post('/bookings', [
            'field_id' => $this->field->id,
            'time_slot_id' => $this->timeSlot->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'customer_name' => 'Test Customer',
            'customer_phone' => '08123456789',
        ]);

        $booking = Booking::where('user_id', $this->member->id)->first();

        // Assert: Check booking lock was created
        $this->assertDatabaseHas('booking_locks', [
            'booking_id' => $booking->id,
            'is_active' => true,
        ]);

        // Assert: Check booking has expires_at set
        $this->assertNotNull($booking->expires_at);
        $this->assertTrue($booking->expires_at->isFuture());
    }

    /**
     * Test that order status is pending
     */
    public function test_auto_created_order_has_pending_status(): void
    {
        // Create booking
        $this->actingAs($this->member)->post('/bookings', [
            'field_id' => $this->field->id,
            'time_slot_id' => $this->timeSlot->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'customer_name' => 'Test Customer',
            'customer_phone' => '08123456789',
        ]);

        $booking = Booking::where('user_id', $this->member->id)->first();
        $order = $booking->orders()->first();

        // Assert
        $this->assertEquals('pending', $order->status);
        $this->assertNull($order->paid_at);
        $this->assertNull($order->xendit_invoice_id);
    }
}
