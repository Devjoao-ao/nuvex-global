<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'superadmin';
    }

    public function view(User $user, Order $model): bool
    {
        return $user->id === $model->user_id || $user->role === 'admin';
    }

    public function update(User $user, Order $model): bool
    {
        return $user->role === 'admin';
    }
}
