<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\Auth\AuthController as ApiAuthController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\DnsController as AdminDnsController;
use App\Http\Controllers\Api\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Api\Admin\BillingController as AdminBillingController;
use App\Http\Controllers\Api\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Api\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\Api\Customer\DomainController as CustomerDomainController;
use App\Http\Controllers\Api\Customer\HostingController as CustomerHostingController;
use App\Http\Controllers\Api\Customer\EmailController as CustomerEmailController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\Customer\BillingController as CustomerBillingController;
use App\Http\Controllers\Api\Customer\TicketController as CustomerTicketController;
use App\Http\Controllers\Api\Customer\RequestController as CustomerRequestController;
use App\Http\Controllers\Api\Customer\NotificationController as CustomerNotificationController;
use App\Http\Controllers\Api\Customer\AccountController as CustomerAccountController;
use App\Http\Controllers\Api\DomainSearchController;

// Domain search (public)
Route::get('/domain/search', [DomainSearchController::class, 'search']);
Route::get('/domain/whois', [DomainSearchController::class, 'whois']);

// Public routes
Route::post('/auth/register', [ApiAuthController::class, 'register']);
Route::post('/auth/login', [ApiAuthController::class, 'login']);
Route::get('/plans', [PlanController::class, 'index']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);
    Route::get('/auth/me', [ApiAuthController::class, 'me']);

    // Customer routes
    Route::prefix('customer')->group(function () {
        Route::get('/services', [CustomerServiceController::class, 'index']);
        Route::get('/services/{service}', [CustomerServiceController::class, 'show']);
        Route::get('/services/{service}/credentials', [CustomerServiceController::class, 'getCredentials']);

        Route::get('/domains', [CustomerDomainController::class, 'index']);
        Route::get('/domains/{domain}', [CustomerDomainController::class, 'show']);
        Route::post('/domains/{domain}/ns', [CustomerDomainController::class, 'updateNs']);
        Route::post('/domains/{domain}/dns', [CustomerDomainController::class, 'updateDns']);

        Route::get('/hosting', [CustomerHostingController::class, 'index']);
        Route::get('/hosting/{hosting}', [CustomerHostingController::class, 'show']);

        Route::get('/emails', [CustomerEmailController::class, 'index']);
        Route::get('/emails/{emailService}', [CustomerEmailController::class, 'show']);

        Route::get('/orders', [CustomerOrderController::class, 'index']);
        Route::post('/orders', [CustomerOrderController::class, 'store']);
        Route::get('/orders/{order}', [CustomerOrderController::class, 'show']);

        Route::get('/billing', [CustomerBillingController::class, 'index']);
        Route::get('/billing/{invoice}', [CustomerBillingController::class, 'show']);

        Route::get('/tickets', [CustomerTicketController::class, 'index']);
        Route::post('/tickets', [CustomerTicketController::class, 'store']);
        Route::get('/tickets/{ticket}', [CustomerTicketController::class, 'show']);
        Route::post('/tickets/{ticket}/reply', [CustomerTicketController::class, 'reply']);
        Route::post('/tickets/{ticket}/close', [CustomerTicketController::class, 'close']);

        Route::get('/requests', [CustomerRequestController::class, 'index']);
        Route::post('/requests', [CustomerRequestController::class, 'store']);
        Route::get('/requests/{request}', [CustomerRequestController::class, 'show']);

        Route::get('/notifications', [CustomerNotificationController::class, 'index']);
        Route::post('/notifications/{notification}/read', [CustomerNotificationController::class, 'markRead']);
        Route::post('/notifications/read-all', [CustomerNotificationController::class, 'markAllRead']);

        Route::get('/account', [CustomerAccountController::class, 'getAccount']);
        Route::put('/account/profile', [CustomerAccountController::class, 'updateProfile']);
        Route::put('/account/password', [CustomerAccountController::class, 'changePassword']);
    });
});

// Admin routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'overview']);

        Route::apiResource('users', AdminUserController::class);
        Route::get('/users/{user}/services', [AdminUserController::class, 'getUserServices']);
        Route::get('/users/{user}/orders', [AdminUserController::class, 'getUserOrders']);
        Route::get('/users/{user}/billing', [AdminUserController::class, 'getUserBilling']);
        Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive']);

        Route::apiResource('plans', AdminPlanController::class);
        Route::post('/plans/{plan}/toggle-active', [AdminPlanController::class, 'toggleActive']);

        Route::apiResource('orders', AdminOrderController::class)->only(['index', 'show']);
        Route::post('/orders/{order}/confirm-payment', [AdminOrderController::class, 'confirmPayment']);
        Route::post('/orders/{order}/process', [AdminOrderController::class, 'processOrder']);
        Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancelOrder']);

        Route::apiResource('services', AdminServiceController::class)->only(['index', 'show']);
        Route::post('/services/{service}/activate', [AdminServiceController::class, 'activate']);
        Route::post('/services/{service}/suspend', [AdminServiceController::class, 'suspend']);
        Route::post('/services/{service}/cancel', [AdminServiceController::class, 'cancel']);
        Route::post('/services/{service}/transfer', [AdminServiceController::class, 'transfer']);
        Route::post('/services/{service}/credentials', [AdminServiceController::class, 'addCredential']);
        Route::put('/credentials/{credential}', [AdminServiceController::class, 'updateCredential']);
        Route::delete('/credentials/{credential}', [AdminServiceController::class, 'deleteCredential']);

        Route::put('/dns/{domain}/ns', [AdminDnsController::class, 'updateNameservers']);
        Route::put('/dns/{domain}/ip', [AdminDnsController::class, 'updateIp']);
        Route::put('/dns/{domain}/dns', [AdminDnsController::class, 'updateDns']);

        Route::apiResource('tickets', AdminTicketController::class)->only(['index', 'show']);
        Route::post('/tickets/{ticket}/assign', [AdminTicketController::class, 'assign']);
        Route::post('/tickets/{ticket}/reply', [AdminTicketController::class, 'reply']);
        Route::post('/tickets/{ticket}/status', [AdminTicketController::class, 'changeStatus']);

        Route::get('/billing', [AdminBillingController::class, 'index']);
        Route::get('/billing/{invoice}', [AdminBillingController::class, 'show']);
        Route::post('/billing/{invoice}/mark-paid', [AdminBillingController::class, 'markPaid']);

        Route::apiResource('requests', AdminRequestController::class)->only(['index', 'show']);
        Route::post('/requests/{request}/handle', [AdminRequestController::class, 'handle']);

        Route::get('/activity', [AdminActivityController::class, 'index']);
        Route::get('/activity/{type}/{id}', [AdminActivityController::class, 'entity']);
    });
});
