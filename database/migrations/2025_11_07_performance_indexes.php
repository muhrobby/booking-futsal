<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add performance indexes
     */
    public function up(): void
    {
        // Speed up date range queries on bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['booking_date'], 'idx_bookings_booking_date');
            $table->index(['user_id', 'booking_date'], 'idx_bookings_user_booking_date');
            $table->index(['status', 'booking_date'], 'idx_bookings_status_booking_date');
        });

        // Speed up user queries
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role'], 'idx_users_role');
            $table->index(['email_verified_at'], 'idx_users_email_verified');
        });

        // Speed up time slot queries
        Schema::table('time_slots', function (Blueprint $table) {
            $table->index(['is_active'], 'idx_time_slots_active');
        });

        // Speed up field queries
        Schema::table('fields', function (Blueprint $table) {
            $table->index(['is_active'], 'idx_fields_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_booking_date');
            $table->dropIndex('idx_bookings_user_booking_date');
            $table->dropIndex('idx_bookings_status_booking_date');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_email_verified');
        });

        Schema::table('time_slots', function (Blueprint $table) {
            $table->dropIndex('idx_time_slots_active');
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->dropIndex('idx_fields_active');
        });
    }
};
