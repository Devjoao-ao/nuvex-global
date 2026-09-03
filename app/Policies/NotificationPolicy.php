<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification as NotificationModel;

class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, NotificationModel $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function update(User $user, NotificationModel $model): bool
    {
        return $user->id === $model->user_id;
    }
}
