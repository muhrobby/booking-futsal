<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Field;
use App\Models\Order;
use App\Models\TimeSlot;
use App\Models\User;
use App\Services\OrderService;
use App\Services\XenditPaymentService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    private User $user;
    private Field $field;
    private TimeSlot $timeSlot;
    private Booking $booking;
    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock HTTP untuk mencegah request ke internet
        Http::preventStrayRequests();

        // Create test data
        $this->user = User::factory()->create();
        $this->field = Field::factory()->create(['price_per_hour' => 175000]);
        $this->timeSlot = TimeSlot::factory()->create();

        // Create booking
        $this->booking = Booking::create([
            'field_id' => $this->field->id,
            'time_slot_id' => $this->timeSlot->id,
            'booking_date' => today()->addDays(1),
            'customer_name' => 'Test Customer',
            'customer_phone' => '08123456789',
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->orderService = app(OrderService::class);
    }

    /**
     * Test: Create order dengan mock XenditPaymentService
     */
    public function test_create_order_with_mocked_xendit(): void
    {
        // Mock Xendit API response
        Http::fake([
            'api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv_mock_12345',
                'invoice_url' => 'https://checkout-staging.xendit.co/web/inv_mock_12345',
                'status' => 'PENDING',
                'amount' => 175000,
                'currency' => 'IDR',
                'description' => 'Booking Futsal - ORD-20251113-abc123',
                'created' => now()->toIso8601String(),
                'updated' => now()->toIso8601String(),
            ], 201),
        ]);

        // Create order
        $order = $this->orderService->createOrder($this->booking, $this->user);

        // Assertions
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(175000, $order->total);
        $this->assertEquals('pending', $this->booking->fresh()->status);
        $this->assertNotNull($this->booking->fresh()->expires_at);
    }

    /**
     * Test: Process payment dengan mock response
     */
    public function test_process_payment_with_mocked_xendit(): void
    {
        // Create order first
        $order = $this->orderService->createOrder($this->booking, $this->user);

        // Mock Xendit response for createInvoice
        Http::fake([
            'api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv_mock_12345',
                'external_id' => $order->order_number,
                'invoice_url' => 'https://checkout-staging.xendit.co/web/inv_mock_12345',
                'status' => 'PENDING',
                'amount' => 175000,
                'currency' => 'IDR',
                'created' => now()->toIso8601String(),
                'expiry_date' => now()->addMinutes(30)->toIso8601String(),
            ], 201),
        ]);

        // Process payment
        $result = $this->orderService->processPayment($order);

        // Debug: Print error if exists
        if (!$result['success']) {
            fwrite(STDERR, "Error: " . $result['error'] . "\n");
        }

        // Assertions
        $this->assertTrue($result['success'], "Process payment failed: " . ($result['error'] ?? 'unknown error'));
        $this->assertNotNull($result['redirect_url']);
        $this->assertEquals('processing', $order->fresh()->status);
    }

    /**
     * Test: Handle payment success dengan mock
     */
    public function test_handle_payment_success_with_mock(): void
    {
        // Create order
        $order = $this->orderService->createOrder($this->booking, $this->user);
        $order->update(['status' => 'processing']);

        // Mock webhook data dari Xendit
        $webhookData = [
            'id' => 'inv_mock_12345',
            'status' => 'PAID',
            'amount' => 175000,
            'currency' => 'IDR',
            'payment_method' => 'bank_transfer',
            'paid_at' => now()->toIso8601String(),
        ];

        // Handle payment success
        $this->orderService->handlePaymentSuccess($order, $webhookData);

        // Assertions
        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals('confirmed', $this->booking->fresh()->status);
        $this->assertNull($this->booking->fresh()->expires_at);
    }

    /**
     * Test: Handle payment failed dengan mock
     */
    public function test_handle_payment_failed_with_mock(): void
    {
        // Create order
        $order = $this->orderService->createOrder($this->booking, $this->user);

        // Handle payment failed
        $this->orderService->handlePaymentFailed($order, 'User cancelled payment');

        // Assertions
        $this->assertEquals('failed', $order->fresh()->status);
        $this->assertEquals('pending', $this->booking->fresh()->status);
        $this->assertNull($this->booking->fresh()->expires_at);
    }

    /**
     * Test: Multiple payments scenario (retry)
     */
    public function test_payment_retry_scenario(): void
    {
        // Create order
        $order = $this->orderService->createOrder($this->booking, $this->user);

        // First attempt failed
        $this->orderService->handlePaymentFailed($order, 'Network error');
        $this->assertEquals('failed', $order->fresh()->status);
        $this->assertEquals('pending', $this->booking->fresh()->status);

        // User retry: Create new order for same booking
        $order2 = $this->orderService->createOrder($this->booking, $this->user);

        // Second attempt success
        $this->orderService->handlePaymentSuccess($order2, [
            'id' => 'inv_mock_67890',
            'status' => 'PAID',
        ]);

        $this->assertEquals('paid', $order2->fresh()->status);
        $this->assertEquals('confirmed', $this->booking->fresh()->status);
    }

    /**
     * Test: Invoice status check mock
     */
    public function test_check_invoice_status_mock(): void
    {
        // Mock Xendit check status endpoint using regex pattern
        Http::fake([
            'api.xendit.co/v2/invoices/*' => Http::response([
                'id' => 'inv_mock_12345',
                'status' => 'PAID',
                'amount' => 175000,
                'currency' => 'IDR',
                'paid_at' => now()->toIso8601String(),
            ], 200),
        ]);

        $xenditService = app(XenditPaymentService::class);
        $invoiceData = $xenditService->checkInvoiceStatus('inv_mock_12345');

        $this->assertNotNull($invoiceData);
        $this->assertEquals('PAID', $invoiceData['status']);
        $this->assertEquals(175000, $invoiceData['amount']);
    }
}
