<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Services\OrderService;
use App\Services\XenditPaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind PaymentGatewayInterface to XenditPaymentService
        $this->app->bind(
            PaymentGatewayInterface::class,
            XenditPaymentService::class
        );

        // Singleton for OrderService
        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(XenditPaymentService::class)
            );
        });

        // Singleton for XenditPaymentService
        $this->app->singleton(XenditPaymentService::class, function ($app) {
            return new XenditPaymentService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
