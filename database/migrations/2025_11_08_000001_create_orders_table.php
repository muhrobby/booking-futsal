<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            return; // Table already exists
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('restrict');
            
            // Order Information
            $table->string('order_number')->unique()->comment('Unique order number for display');
            $table->enum('status', [
                'pending',      // Awaiting payment
                'processing',   // Payment being processed
                'paid',         // Payment successful
                'failed',       // Payment failed
                'expired',      // Payment timeout (30 mins)
                'refunded',     // Full refund processed
                'cancelled'     // Cancelled by user/admin
            ])->default('pending')->index();
            
            // Payment Details
            $table->decimal('subtotal', 12, 2)->comment('Amount before tax/discount');
            $table->decimal('tax', 12, 2)->default(0)->comment('Tax amount');
            $table->decimal('discount', 12, 2)->default(0)->comment('Discount amount');
            $table->decimal('total', 12, 2)->comment('Final amount to pay');
            $table->string('currency', 3)->default('IDR')->comment('Currency code');
            
            // Payment Method & Reference
            $table->string('payment_method')->nullable()->comment('Card, e-wallet, bank transfer, etc');
            $table->string('payment_reference')->nullable()->unique()->comment('Xendit invoice reference');
            $table->string('xendit_invoice_id')->nullable()->unique()->comment('Xendit invoice ID');
            
            // Timestamps
            $table->timestamp('paid_at')->nullable()->comment('When payment was completed');
            $table->timestamp('expired_at')->nullable()->comment('Payment expiration time');
            $table->text('admin_notes')->nullable()->comment('Admin notes for this order');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('booking_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
