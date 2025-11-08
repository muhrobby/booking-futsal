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
        if (Schema::hasTable('payment_transactions')) {
            return; // Table already exists
        }

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Gateway Information
            $table->string('gateway')->default('xendit')->comment('Payment gateway used');
            $table->string('gateway_transaction_id')->nullable()->unique()->comment('Transaction ID from Xendit');
            $table->string('gateway_invoice_id')->nullable()->unique()->comment('Invoice ID from Xendit');
            
            // Transaction Status
            $table->enum('status', [
                'pending',      // Waiting for payment
                'processing',   // Being processed by gateway
                'completed',    // Successfully paid
                'failed',       // Payment failed
                'expired',      // Timeout
                'cancelled',    // Cancelled
                'refunded'      // Refunded
            ])->default('pending')->index();
            
            // Amount Details
            $table->decimal('amount', 12, 2)->comment('Transaction amount');
            $table->string('currency', 3)->default('IDR');
            
            // Payment Method
            $table->string('payment_method')->nullable()->comment('e.g., CREDIT_CARD, OVO, BANK_TRANSFER');
            $table->string('payment_method_detail')->nullable()->comment('Additional details');
            
            // Request/Response Payloads
            $table->longText('request_payload')->nullable()->comment('Request sent to Xendit');
            $table->longText('response_payload')->nullable()->comment('Response from Xendit');
            
            // Error Handling
            $table->text('error_message')->nullable()->comment('Error message if failed');
            $table->string('error_code')->nullable()->comment('Error code from gateway');
            
            // Webhook Information
            $table->timestamp('webhook_received_at')->nullable()->comment('When webhook was received');
            $table->text('webhook_payload')->nullable()->comment('Raw webhook data');
            
            // Refund Information
            $table->decimal('refunded_amount', 12, 2)->nullable()->comment('Amount refunded');
            $table->timestamp('refunded_at')->nullable()->comment('When refund was processed');
            
            $table->timestamps();
            
            // Indexes
            $table->index('order_id');
            $table->index('gateway_transaction_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
