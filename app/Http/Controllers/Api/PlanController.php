<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Plan::active();

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        $plans = $query->orderBy('sort_order', 'asc')->get();

        return response()->json([
            'plans' => $plans,
        ]);
    }
}
