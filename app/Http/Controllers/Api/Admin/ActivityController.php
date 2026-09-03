<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with(['user']);

        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($activities);
    }

    public function entity(string $type, int $id): JsonResponse
    {
        $activities = ActivityLog::with(['user'])
            ->byEntity($type, $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($activities);
    }
}
