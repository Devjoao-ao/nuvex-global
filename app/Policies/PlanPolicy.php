<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Plan;

class PlanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'superadmin';
    }

    public function update(User $user, Plan $model): bool
    {
        return $user->role === 'admin' || $user->role === 'superadmin';
    }

    public function delete(User $user, Plan $model): bool
    {
        return $user->role === 'superadmin';
    }
}
