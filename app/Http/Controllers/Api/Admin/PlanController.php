<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Plan::query();

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $plans = $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($plans);
    }

    public function store(StorePlanRequest $request): JsonResponse
    {
        $plan = Plan::create($request->validated());

        return response()->json([
            'message' => 'Plano criado com sucesso.',
            'plan' => $plan,
        ], 201);
    }

    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        $plan->update($request->validated());

        return response()->json([
            'message' => 'Plano atualizado com sucesso.',
            'plan' => $plan,
        ]);
    }

    public function toggleActive(Plan $plan): JsonResponse
    {
        $plan->update(['active' => !$plan->active]);

        return response()->json([
            'message' => 'Status do plano alterado com sucesso.',
            'plan' => $plan,
        ]);
    }
}
