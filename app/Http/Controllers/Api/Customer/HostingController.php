<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Hosting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HostingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hosting = $request->user()
            ->hosting()
            ->with(['service', 'domain'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($hosting);
    }

    public function show(Request $request, Hosting $hosting): JsonResponse
    {
        if ($hosting->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Hospedagem não encontrada.',
            ], 404);
        }

        $hosting->load(['service', 'domain']);

        return response()->json([
            'hosting' => $hosting,
        ]);
    }
}
