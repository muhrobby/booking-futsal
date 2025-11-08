<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id','time_slot_id','booking_date',
        'customer_name','customer_phone','status','notes','user_id',
        'start_time','end_time','total_price'
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function field(): BelongsTo {
        return $this->belongsTo(Field::class);
    }

    public function timeSlot(): BelongsTo {
        return $this->belongsTo(TimeSlot::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Payment Relations
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function bookingLocks(): HasMany
    {
        return $this->hasMany(BookingLock::class);
    }

    /**
     * Payment Methods
     */
    public function isLocked(): bool
    {
        return $this->getActiveLock() !== null;
    }

    public function getActiveLock(): ?BookingLock
    {
        return $this->bookingLocks()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function lockForPayment(Order $order): BookingLock
    {
        return $this->bookingLocks()->create([
            'order_id' => $order->id,
            'reason' => 'payment_pending',
            'is_active' => true,
            'expires_at' => now()->addMinutes(30),
        ]);
    }

    public function releaseLock(string $reason = ''): void
    {
        $this->bookingLocks()
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'released_at' => now(),
                'released_reason' => $reason,
            ]);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    public function scopeBooked(Builder $query): Builder
    {
        return $query->where('status', 'booked');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }
}
