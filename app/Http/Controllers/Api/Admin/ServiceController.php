<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCredentialRequest;
use App\Http\Requests\TransferServiceRequest;
use App\Http\Requests\UpdateCredentialRequest;
use App\Models\Service;
use App\Models\ServiceCredential;
use App\Models\ServiceTransfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::with(['user', 'plan', 'domain', 'hosting', 'emailService']);

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($services);
    }

    public function show(Service $service): JsonResponse
    {
        $service->load([
            'user', 'order', 'plan', 'domain', 'hosting',
            'emailService', 'credentials', 'transfersFrom', 'transfersTo',
        ]);

        return response()->json([
            'service' => $service,
        ]);
    }

    public function activate(Service $service): JsonResponse
    {
        if ($service->status === 'active') {
            return response()->json([
                'message' => 'O serviço já está ativo.',
            ], 422);
        }

        $service->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Serviço ativado com sucesso.',
            'service' => $service,
        ]);
    }

    public function suspend(Service $service): JsonResponse
    {
        if ($service->status !== 'active') {
            return response()->json([
                'message' => 'Apenas serviços ativos podem ser suspensos.',
            ], 422);
        }

        $service->update(['status' => 'suspended']);

        return response()->json([
            'message' => 'Serviço suspenso com sucesso.',
            'service' => $service,
        ]);
    }

    public function cancel(Service $service): JsonResponse
    {
        if ($service->status === 'cancelled') {
            return response()->json([
                'message' => 'O serviço já está cancelado.',
            ], 422);
        }

        $service->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Serviço cancelado com sucesso.',
            'service' => $service,
        ]);
    }

    public function transfer(TransferServiceRequest $request, Service $service): JsonResponse
    {
        $transfer = ServiceTransfer::create([
            'service_id' => $service->id,
            'from_user_id' => $service->user_id,
            'to_user_id' => $request->to_user_id,
            'admin_id' => $request->user()->id,
            'reason' => $request->reason,
        ]);

        $service->update(['user_id' => $request->to_user_id]);

        $service->load(['user', 'plan', 'domain', 'hosting', 'emailService']);

        return response()->json([
            'message' => 'Serviço transferido com sucesso.',
            'service' => $service,
            'transfer' => $transfer,
        ]);
    }

    public function addCredential(StoreCredentialRequest $request, Service $service): JsonResponse
    {
        $credential = $service->credentials()->create($request->validated());

        return response()->json([
            'message' => 'Credencial adicionada com sucesso.',
            'credential' => $credential,
        ], 201);
    }

    public function updateCredential(UpdateCredentialRequest $request, ServiceCredential $credential): JsonResponse
    {
        $credential->update($request->validated());

        return response()->json([
            'message' => 'Credencial atualizada com sucesso.',
            'credential' => $credential,
        ]);
    }

    public function deleteCredential(ServiceCredential $credential): JsonResponse
    {
        $credential->delete();

        return response()->json([
            'message' => 'Credencial removida com sucesso.',
        ]);
    }
}
