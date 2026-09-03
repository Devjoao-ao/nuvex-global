<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Service;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Service $model): bool
    {
        return $user->id === $model->user_id || $user->role === 'admin' || $user->role === 'superadmin';
    }

    public function update(User $user, Service $model): bool
    {
        return $user->role === 'admin' || $user->role === 'superadmin';
    }

    public function delete(User $user, Service $model): bool
    {
        return $user->role === 'superadmin';
    }

    public function transfer(User $user, Service $model): bool
    {
        return $user->role === 'admin' || $user->role === 'superadmin';
    }

    public function activate(User $user, Service $model): bool
    {
        return $user->role === 'admin';
    }

    public function suspend(User $user, Service $model): bool
    {
        return $user->role === 'admin';
    }
}
