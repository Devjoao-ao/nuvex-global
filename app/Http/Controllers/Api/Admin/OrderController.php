<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\NotificationService;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private NotificationService $notificationService,
        private ActivityService $activityService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'items', 'payments']);

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $query->where('number', 'like', "%{$request->search}%");
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($orders);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['user', 'items', 'items.plan', 'payments', 'services', 'invoices']);

        return response()->json([
            'order' => $order,
        ]);
    }

    public function confirmPayment(Order $order, Request $request): JsonResponse
    {
        if (!in_array($order->status, ['pending'])) {
            return response()->json([
                'message' => 'Este pedido não pode ter o pagamento confirmado.',
            ], 422);
        }

        $this->orderService->confirmPayment($order, [
            'reference' => $request->input('reference', $order->payment_reference),
        ]);

        $this->activityService->log(
            $request->user(),
            'confirmed_payment',
            'Order',
            $order->id,
            $order->number,
            ['status' => 'pending'],
            ['status' => 'processing', 'paid_at' => now()],
            "Pagamento confirmado para pedido {$order->number}"
        );

        $this->notificationService->sendToUser(
            $order->user,
            'order',
            'paid',
            'Pagamento Confirmado',
            "O pagamento do seu pedido {$order->number} foi confirmado.",
            ['order_number' => $order->number, 'amount' => $order->total]
        );

        return response()->json([
            'message' => 'Pagamento confirmado com sucesso.',
            'order' => $order->fresh(['user', 'items', 'payments']),
        ]);
    }

    public function processOrder(Order $order, Request $request): JsonResponse
    {
        if (!in_array($order->status, ['processing'])) {
            return response()->json([
                'message' => 'Este pedido não pode ser processado.',
            ], 422);
        }

        $this->orderService->processOrder($order);

        $this->activityService->log(
            $request->user(),
            'processed_order',
            'Order',
            $order->id,
            $order->number,
            ['status' => 'processing'],
            ['status' => 'completed'],
            "Pedido {$order->number} processado e serviços criados"
        );

        $this->notificationService->sendToUser(
            $order->user,
            'order',
            'completed',
            'Pedido Concluído',
            "O seu pedido {$order->number} foi processado com sucesso. Os seus serviços já estão disponíveis.",
            ['order_number' => $order->number]
        );

        return response()->json([
            'message' => 'Pedido processado com sucesso.',
            'order' => $order->fresh(['user', 'items', 'services']),
        ]);
    }

    public function cancelOrder(Order $order, Request $request): JsonResponse
    {
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'Este pedido não pode ser cancelado.',
            ], 422);
        }

        $oldStatus = $order->status;
        $order->update(['status' => 'cancelled']);

        $this->activityService->log(
            $request->user(),
            'cancelled_order',
            'Order',
            $order->id,
            $order->number,
            ['status' => $oldStatus],
            ['status' => 'cancelled'],
            "Pedido {$order->number} cancelado"
        );

        $this->notificationService->sendToUser(
            $order->user,
            'order',
            'cancelled',
            'Pedido Cancelado',
            "O seu pedido {$order->number} foi cancelado.",
            ['order_number' => $order->number]
        );

        return response()->json([
            'message' => 'Pedido cancelado com sucesso.',
            'order' => $order,
        ]);
    }
}
