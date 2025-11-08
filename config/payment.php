<?php

return [
    'xendit' => [
        'mode' => env('XENDIT_ENVIRONMENT', 'sandbox'),
        'secret_key' => env('XENDIT_SECRET_KEY'),
        'public_key' => env('XENDIT_PUBLIC_KEY'),
        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
        'callback_url' => env('XENDIT_CALLBACK_URL'),
    ],

    'payment' => [
        'timeout_minutes' => env('PAYMENT_TIMEOUT_MINUTES', 30),
        'currency' => env('PAYMENT_CURRENCY', 'IDR'),
        'description_template' => 'Booking Futsal - {order_number}',
    ],

    'methods' => [
        'credit_card' => [
            'enabled' => true,
            'fees_percentage' => 2.9,
            'minimum' => 10000,
        ],
        'debit_card' => [
            'enabled' => true,
            'fees_percentage' => 0.5,
            'minimum' => 10000,
        ],
        'e_wallet' => [
            'enabled' => true,
            'methods' => ['ovo', 'dana', 'linkaja', 'gopay'],
            'fees_percentage' => 1.5,
            'minimum' => 1000,
        ],
        'bank_transfer' => [
            'enabled' => true,
            'banks' => ['bca', 'mandiri', 'bni', 'permata', 'danamon'],
            'fees_percentage' => 0,
            'minimum' => 1000,
        ],
        'bnpl' => [
            'enabled' => true,
            'providers' => ['kredivo', 'akulaku'],
            'fees_percentage' => 2.5,
            'minimum' => 100000,
        ],
        'retail' => [
            'enabled' => true,
            'stores' => ['indomaret', 'alfamart'],
            'fees_percentage' => 2.5,
            'minimum' => 10000,
        ],
    ],

    'status' => [
        'pending' => 'PENDING',
        'processing' => 'PROCESSING',
        'paid' => 'PAID',
        'failed' => 'FAILED',
        'expired' => 'EXPIRED',
        'refunded' => 'REFUNDED',
        'cancelled' => 'CANCELLED',
    ],
];
