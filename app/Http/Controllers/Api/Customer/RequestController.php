<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestRequest;
use App\Models\Request as ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $requests = $request->user()
            ->requests()
            ->with(['handler'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($requests);
    }

    public function store(StoreRequestRequest $request): JsonResponse
    {
        $serviceRequest = ServiceRequest::create([
            'number' => 'REQ-' . strtoupper(uniqid()),
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'service_type' => $request->service_type,
            'service_id' => $request->service_id,
            'status' => 'pending',
            'data' => $request->data,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Solicitação criada com sucesso.',
            'request' => $serviceRequest,
        ], 201);
    }

    public function show(Request $request, ServiceRequest $req): JsonResponse
    {
        if ($req->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Solicitação não encontrada.',
            ], 404);
        }

        $req->load('handler');

        return response()->json([
            'request' => $req,
        ]);
    }
}
