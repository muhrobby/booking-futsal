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
        if (Schema::hasTable('booking_locks')) {
            return; // Table already exists
        }

        Schema::create('booking_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade');
            
            // Lock Information
            $table->timestamp('locked_at')->useCurrent()->comment('When lock was created');
            $table->timestamp('expires_at')->comment('When lock expires (30 mins)');
            $table->string('reason')->default('payment_pending')->comment('Why slot is locked');
            
            // Lock Status
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('released_at')->nullable()->comment('When lock was released early');
            $table->string('released_reason')->nullable()->comment('Why lock was released');
            
            $table->timestamps();
            
            // Unique constraint: only one active lock per booking
            $table->unique(['booking_id', 'order_id']);
            
            // Indexes
            $table->index('booking_id');
            $table->index('order_id');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_locks');
    }
};
