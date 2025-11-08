<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Create an invoice
     *
     * @param Order $order
     * @return array
     */
    public function createInvoice(Order $order): array;

    /**
     * Get invoice status
     *
     * @param string $invoiceId
     * @return array
     */
    public function getInvoiceStatus(string $invoiceId): array;

    /**
     * Verify webhook signature
     *
     * @param string $payload
     * @param string $signature
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool;

    /**
     * Process webhook
     *
     * @param array $payload
     * @return array
     */
    public function processWebhook(array $payload): array;

    /**
     * Refund invoice
     *
     * @param string $invoiceId
     * @param float $amount
     * @return array
     */
    public function refundInvoice(string $invoiceId, float $amount): array;
}
