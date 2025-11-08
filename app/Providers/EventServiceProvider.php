<?php

namespace App\Providers;

use App\Events\PaymentExpiredEvent;
use App\Events\PaymentFailedEvent;
use App\Events\PaymentSuccessfulEvent;
use App\Listeners\SendPaymentExpiredNotification;
use App\Listeners\SendPaymentFailedNotification;
use App\Listeners\SendPaymentSuccessfulNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        PaymentSuccessfulEvent::class => [
            SendPaymentSuccessfulNotification::class,
        ],
        PaymentFailedEvent::class => [
            SendPaymentFailedNotification::class,
        ],
        PaymentExpiredEvent::class => [
            SendPaymentExpiredNotification::class,
        ],
    ];

    /**
     * Enable the jobs queue to be used for events.
     */
    public bool $queue = true;
}
