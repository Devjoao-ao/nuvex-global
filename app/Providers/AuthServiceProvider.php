<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Service;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Request;
use App\Models\Notification as NotificationModel;
use App\Policies\UserPolicy;
use App\Policies\ServicePolicy;
use App\Policies\OrderPolicy;
use App\Policies\TicketPolicy;
use App\Policies\PlanPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\RequestPolicy;
use App\Policies\NotificationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Service::class => ServicePolicy::class,
        Order::class => OrderPolicy::class,
        Ticket::class => TicketPolicy::class,
        Plan::class => PlanPolicy::class,
        Invoice::class => InvoicePolicy::class,
        Request::class => RequestPolicy::class,
        NotificationModel::class => NotificationPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
