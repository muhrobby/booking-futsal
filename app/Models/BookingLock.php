<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingLock extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'order_id',
        'locked_at',
        'expires_at',
        'reason',
        'is_active',
        'released_at',
        'released_reason',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'expires_at' => 'datetime',
        'released_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scopes
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Methods
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->expires_at > now();
    }

    public function hasExpired(): bool
    {
        return $this->expires_at <= now();
    }

    public function release(string $reason = ''): void
    {
        $this->update([
            'is_active' => false,
            'released_at' => now(),
            'released_reason' => $reason,
        ]);
    }

    public function extend(int $minutes = 30): void
    {
        $this->update([
            'expires_at' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (BookingLock $lock) {
            // Default expiry: 30 minutes from now if not specified
            if (!$lock->expires_at) {
                $lock->expires_at = now()->addMinutes(30);
            }

            // Default reason
            if (!$lock->reason) {
                $lock->reason = 'payment_pending';
            }
        });
    }
}
