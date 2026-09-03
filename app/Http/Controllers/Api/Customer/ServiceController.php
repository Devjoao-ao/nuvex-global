<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $services = $request->user()
            ->services()
            ->with(['plan', 'domain', 'hosting', 'emailService'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($services);
    }

    public function show(Request $request, Service $service): JsonResponse
    {
        if ($service->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Serviço não encontrado.',
            ], 404);
        }

        $service->load(['plan', 'domain', 'hosting', 'emailService', 'credentials' => function ($query) {
            $query->where('is_visible_to_customer', true);
        }]);

        return response()->json([
            'service' => $service,
        ]);
    }

    public function getCredentials(Request $request, Service $service): JsonResponse
    {
        if ($service->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Serviço não encontrado.',
            ], 404);
        }

        $credentials = $service->credentials()
            ->where('is_visible_to_customer', true)
            ->get();

        return response()->json([
            'credentials' => $credentials,
        ]);
    }
}
