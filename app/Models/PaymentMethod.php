<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'last_four',
        'brand',
        'token',
        'gateway_customer_id',
        'gateway_payment_method_id',
        'is_default',
        'is_active',
        'expiry_month',
        'expiry_year',
        'name',
        'email',
        'phone',
        'metadata',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'json',
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

    /**
     * Scopes
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Methods
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isDefault(): bool
    {
        return $this->is_default;
    }

    public function setAsDefault(): void
    {
        // Remove default from other payment methods
        $this->user->paymentMethods()->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);
    }

    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
            'is_default' => false,
        ]);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function getMaskedDisplay(): string
    {
        return match ($this->type) {
            'credit_card', 'debit_card' => "{$this->brand} •••• {$this->last_four}",
            'e_wallet' => "{$this->type} - {$this->name}",
            default => $this->type,
        };
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function (PaymentMethod $method) {
            // If this is the user's first payment method, set it as default
            if ($method->user->paymentMethods()->count() === 1) {
                $method->update(['is_default' => true]);
            }
        });
    }
}
