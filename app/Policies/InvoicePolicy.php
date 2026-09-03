<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Invoice $model): bool
    {
        return $user->id === $model->user_id || $user->role === 'admin';
    }

    public function update(User $user, Invoice $model): bool
    {
        return $user->role === 'admin';
    }
}
