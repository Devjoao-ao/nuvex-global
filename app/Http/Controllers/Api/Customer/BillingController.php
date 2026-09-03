<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $invoices = $request->user()
            ->invoices()
            ->with(['order', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($invoices);
    }

    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Fatura não encontrada.',
            ], 404);
        }

        $invoice->load(['order', 'payment']);

        return response()->json([
            'invoice' => $invoice,
        ]);
    }
}
