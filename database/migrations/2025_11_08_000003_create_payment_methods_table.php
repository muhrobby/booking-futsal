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
        if (Schema::hasTable('payment_methods')) {
            return; // Table already exists
        }

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Payment Method Type
            $table->enum('type', [
                'credit_card',
                'debit_card',
                'e_wallet',
                'bank_transfer',
                'bnpl',
                'retail'
            ])->index();
            
            // Card/Payment Details
            $table->string('last_four')->nullable()->comment('Last 4 digits for card');
            $table->string('brand')->nullable()->comment('Card brand: Visa, Mastercard, etc');
            $table->string('token')->nullable()->comment('Tokenized payment method');
            
            // Xendit References
            $table->string('gateway_customer_id')->nullable()->unique()->comment('Xendit customer ID');
            $table->string('gateway_payment_method_id')->nullable()->unique()->comment('Xendit payment method ID');
            
            // Status
            $table->boolean('is_default')->default(false)->comment('Default payment method for user');
            $table->boolean('is_active')->default(true)->index();
            
            // Expiry (for cards)
            $table->string('expiry_month')->nullable();
            $table->string('expiry_year')->nullable();
            
            // Additional Info
            $table->string('name')->nullable()->comment('Name on card or account');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable()->comment('Additional gateway-specific data');
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
