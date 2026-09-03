<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $model): bool
    {
        return $user->id === $model->user_id || $user->role === 'admin';
    }

    public function update(User $user, Ticket $model): bool
    {
        return $user->role === 'admin' || $user->id === $model->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }
}
