<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_id',
        'order_number',
        'status',
        'subtotal',
        'tax',
        'discount',
        'total',
        'currency',
        'payment_method',
        'payment_reference',
        'xendit_invoice_id',
        'paid_at',
        'expired_at',
        'admin_notes',
    ];

    protected $casts = [
        'status' => 'string',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function bookingLocks(): HasMany
    {
        return $this->hasMany(BookingLock::class);
    }

    /**
     * Scopes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'expired');
    }

    public function scopeRefunded(Builder $query): Builder
    {
        return $query->where('status', 'refunded');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'processing', 'paid']);
    }

    /**
     * Methods
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function canBeRefunded(): bool
    {
        return $this->isPaid() && !$this->isRefunded();
    }

    public function getLastTransaction(): ?PaymentTransaction
    {
        return $this->transactions()->latest()->first();
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Auto-confirm the booking when payment is received
        $this->booking()->update(['status' => 'confirmed']);
    }

    public function markAsFailed(string $reason = ''): void
    {
        $this->update([
            'status' => 'failed',
            'admin_notes' => $reason,
        ]);

        // Release the booking lock
        $this->releaseLock('payment_failed');
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
            'expired_at' => now(),
        ]);

        // Release the booking lock
        $this->releaseLock('payment_expired');

        // Cancel the booking
        $this->booking()->update(['status' => 'cancelled']);
    }

    public function releaseLock(string $reason = ''): void
    {
        BookingLock::where('order_id', $this->id)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'released_at' => now(),
                'released_reason' => $reason,
            ]);
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order) {
            // Generate unique order number if not provided
            if (!$order->order_number) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . uniqid();
            }
        });
    }
}
