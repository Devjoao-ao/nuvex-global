<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Request as RequestModel;

class RequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RequestModel $model): bool
    {
        return $user->id === $model->user_id || $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, RequestModel $model): bool
    {
        return $user->role === 'admin';
    }
}
