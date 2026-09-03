<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->user(),
            $request->validated('items'),
            [
                'method' => $request->validated('payment_method'),
                'reference' => $request->validated('payment_reference'),
            ]
        );

        $order->load(['items']);

        return response()->json([
            'message' => 'Pedido criado com sucesso.',
            'order' => $order,
        ], 201);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Pedido não encontrado.',
            ], 404);
        }

        $order->load(['items', 'payments', 'services', 'invoices']);

        return response()->json([
            'order' => $order,
        ]);
    }
}
