<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Domain;
use App\Models\Hosting;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function overview(): JsonResponse
    {
        $totalUsers = User::where('role', 'customer')->count();
        $totalOrders = Order::count();
        $totalServices = Service::count();

        $servicesByType = Service::select('type', \DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        $recentOrders = Order::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentTickets = Ticket::with(['user', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $totalRevenue = Order::where('status', 'paid')
            ->sum('total');

        $monthlyRevenue = Order::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $activeServices = Service::where('status', 'active')->count();
        $pendingTickets = Ticket::whereNotIn('status', ['closed', 'resolved'])->count();
        $pendingRequests = \App\Models\Request::where('status', 'pending')->count();

        return response()->json([
            'stats' => [
                'total_users' => $totalUsers,
                'total_orders' => $totalOrders,
                'total_services' => $totalServices,
                'active_services' => $activeServices,
                'pending_tickets' => $pendingTickets,
                'pending_requests' => $pendingRequests,
                'total_revenue' => $totalRevenue,
                'monthly_revenue' => $monthlyRevenue,
            ],
            'services_by_type' => $servicesByType,
            'recent_orders' => $recentOrders,
            'recent_tickets' => $recentTickets,
        ]);
    }
}
