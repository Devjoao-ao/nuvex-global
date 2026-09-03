<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\EmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $emailServices = $request->user()
            ->emailServices()
            ->with(['service', 'domain'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($emailServices);
    }

    public function show(Request $request, EmailService $emailService): JsonResponse
    {
        if ($emailService->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Serviço de e-mail não encontrado.',
            ], 404);
        }

        $emailService->load(['service', 'domain', 'accounts']);

        return response()->json([
            'email_service' => $emailService,
        ]);
    }
}
