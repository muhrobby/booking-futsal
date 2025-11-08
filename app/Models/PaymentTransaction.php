<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_transaction_id',
        'gateway_invoice_id',
        'status',
        'amount',
        'currency',
        'payment_method',
        'payment_method_detail',
        'request_payload',
        'response_payload',
        'error_message',
        'error_code',
        'webhook_received_at',
        'webhook_payload',
        'refunded_amount',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'request_payload' => 'json',
        'response_payload' => 'json',
        'webhook_payload' => 'json',
        'webhook_received_at' => 'datetime',
        'refunded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scopes
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'expired');
    }

    public function scopeRefunded(Builder $query): Builder
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Methods
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
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
        return $this->isCompleted() && !$this->isRefunded();
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function markAsFailed(string $errorCode = '', string $errorMessage = ''): void
    {
        $this->update([
            'status' => 'failed',
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function markAsRefunded(float $refundedAmount = 0): void
    {
        $this->update([
            'status' => 'refunded',
            'refunded_amount' => $refundedAmount ?: $this->amount,
            'refunded_at' => now(),
        ]);
    }

    public function getGatewayResponse(): array
    {
        return $this->response_payload ?? [];
    }

    public function getGatewayRequest(): array
    {
        return $this->request_payload ?? [];
    }

    public function getWebhookData(): array
    {
        return $this->webhook_payload ?? [];
    }
}
