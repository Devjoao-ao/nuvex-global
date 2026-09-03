<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['adminProfile']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'document' => $request->document,
            'address' => $request->address,
            'password' => Hash::make('password'),
            'active' => true,
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso.',
            'user' => $user,
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        $user->load(['adminProfile', 'orders', 'services', 'domains', 'hosting', 'emailServices', 'tickets', 'invoices']);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->only([
            'name', 'email', 'phone', 'role', 'document', 'address', 'active',
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso.',
            'user' => $user,
        ]);
    }

    public function toggleActive(User $user): JsonResponse
    {
        $user->update(['active' => !$user->active]);

        return response()->json([
            'message' => 'Status do usuário alterado com sucesso.',
            'user' => $user,
        ]);
    }

    public function getUserServices(User $user): JsonResponse
    {
        $services = $user->services()
            ->with(['plan', 'domain', 'hosting', 'emailService'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'services' => $services,
        ]);
    }

    public function getUserOrders(User $user): JsonResponse
    {
        $orders = $user->orders()
            ->with(['items', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function getUserBilling(User $user): JsonResponse
    {
        $invoices = $user->invoices()
            ->with(['order', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'invoices' => $invoices,
        ]);
    }
}
