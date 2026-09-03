<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'superadmin';
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->role === 'admin' || $user->role === 'superadmin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'superadmin';
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->role === 'admin';
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role === 'superadmin';
    }

    public function toggleActive(User $user, User $model): bool
    {
        return $user->role === 'admin' || $user->role === 'superadmin';
    }
}
