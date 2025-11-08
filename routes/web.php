<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\FieldController as AdminFieldController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/fields', [FieldController::class, 'index'])->name('fields.index');
Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
Route::view('/contact', 'contact')->name('contact');

// Health check endpoints (no authentication required)
Route::get('/health', [HealthController::class, 'check'])->name('health.check');
Route::get('/health/ready', [HealthController::class, 'ready'])->name('health.ready');
Route::get('/health/live', [HealthController::class, 'alive'])->name('health.live');

Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');
Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

Route::middleware('auth')->group(function () {
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.my');

    // Order routes (Customer)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/bookings/{booking}/checkout', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/bookings/{booking}/checkout', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/payment/success', [OrderController::class, 'success'])->name('orders.success');
    Route::get('/orders/payment/failed', [OrderController::class, 'failed'])->name('orders.failed');

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('fields', AdminFieldController::class)->except(['show']);
    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::patch('bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
    
    // Admin Order Management
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('orders/{order}/refund', [AdminOrderController::class, 'refund'])->name('orders.refund');
});

// Xendit Webhook (No authentication - verified by webhook token)
Route::post('/webhooks/xendit', [WebhookController::class, 'handleXenditWebhook'])->name('webhooks.xendit');

require __DIR__ . '/auth.php';
