<?php

namespace Tests\Feature;

use App\Events\PaymentSuccessfulEvent;
use App\Models\Booking;
use App\Models\Field;
use App\Models\Order;
use App\Models\TimeSlot;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Field $field;
    protected TimeSlot $timeSlot;
    protected Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->field = Field::create([
            'name' => 'Lapangan Test',
            'type' => 'futsal',
            'price_per_hour' => 100000,
            'is_active' => true,
        ]);

        $this->timeSlot = TimeSlot::create([
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'is_active' => true,
        ]);

        $this->booking = Booking::create([
            'user_id' => $this->user->id,
            'field_id' => $this->field->id,
            'time_slot_id' => $this->timeSlot->id,
            'booking_date' => now()->addDays(1)->format('Y-m-d'),
            'customer_name' => 'Test Customer',
            'customer_phone' => '081234567890',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_can_create_order_from_booking()
    {
        $orderService = app(OrderService::class);

        $order = $orderService->createOrder($this->booking, $this->user);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals($this->booking->id, $order->booking_id);
        $this->assertEquals($this->user->id, $order->user_id);
        $this->assertEquals(100000, $order->total);
        $this->assertNotNull($order->order_number);
        $this->assertStringStartsWith('ORD-', $order->order_number);

        // Check booking lock created
        $this->assertDatabaseHas('booking_locks', [
            'booking_id' => $this->booking->id,
            'order_id' => $order->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_dispatches_payment_successful_event_on_payment_success()
    {
        Event::fake([PaymentSuccessfulEvent::class]);

        $orderService = app(OrderService::class);
        $order = $orderService->createOrder($this->booking, $this->user);

        $webhookData = [
            'id' => 'test-invoice-id',
            'status' => 'PAID',
            'amount' => 100000,
        ];

        $orderService->handlePaymentSuccess($order, $webhookData);

        Event::assertDispatched(PaymentSuccessfulEvent::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });

        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals('confirmed', $this->booking->fresh()->status);
    }

    /** @test */
    public function webhook_can_handle_paid_status()
    {
        $this->actingAs($this->user);

        $order = Order::create([
            'user_id' => $this->user->id,
            'booking_id' => $this->booking->id,
            'order_number' => 'ORD-' . date('Ymd') . '-test',
            'status' => 'pending',
            'subtotal' => 100000,
            'tax' => 0,
            'discount' => 0,
            'total' => 100000,
            'currency' => 'IDR',
        ]);

        $webhookPayload = [
            'id' => 'invoice-123',
            'external_id' => 'ORDER-' . $order->id,
            'status' => 'PAID',
            'amount' => 100000,
            'paid_at' => now()->toIso8601String(),
        ];

        $response = $this->postJson('/webhooks/xendit', $webhookPayload, [
            'x-callback-token' => config('payment.xendit.webhook_token'),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('paid', $order->fresh()->status);
    }

    /** @test */
    public function it_can_mark_order_as_expired()
    {
        $orderService = app(OrderService::class);
        $order = $orderService->createOrder($this->booking, $this->user);

        $orderService->handlePaymentExpired($order);

        $this->assertEquals('expired', $order->fresh()->status);
        $this->assertEquals('cancelled', $this->booking->fresh()->status);

        // Check booking lock released
        $this->assertDatabaseHas('booking_locks', [
            'booking_id' => $this->booking->id,
            'order_id' => $order->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function customer_can_access_checkout_page()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('orders.create', $this->booking));

        $response->assertStatus(200);
        $response->assertViewIs('orders.create');
        $response->assertViewHas('booking', $this->booking);
    }

    /** @test */
    public function customer_cannot_access_others_checkout()
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        $response = $this->get(route('orders.create', $this->booking));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_orders_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Order::create([
            'user_id' => $this->user->id,
            'booking_id' => $this->booking->id,
            'order_number' => 'ORD-TEST-001',
            'status' => 'pending',
            'subtotal' => 100000,
            'total' => 100000,
            'currency' => 'IDR',
        ]);

        $response = $this->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.index');
        $response->assertSee('ORD-TEST-001');
    }
}
