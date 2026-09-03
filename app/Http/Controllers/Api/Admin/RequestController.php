<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HandleRequestRequest;
use App\Models\Request as ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ServiceRequest::with(['user', 'handler']);

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($requests);
    }

    public function show(ServiceRequest $serviceRequest): JsonResponse
    {
        $serviceRequest->load(['user', 'handler']);

        return response()->json([
            'request' => $serviceRequest,
        ]);
    }

    public function handle(HandleRequestRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        if ($serviceRequest->status !== 'pending') {
            return response()->json([
                'message' => 'Esta solicitação já foi processada.',
            ], 422);
        }

        $serviceRequest->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'handled_by' => $request->user()->id,
            'handled_at' => now(),
        ]);

        return response()->json([
            'message' => 'Solicitação processada com sucesso.',
            'request' => $serviceRequest->fresh(['user', 'handler']),
        ]);
    }
}
