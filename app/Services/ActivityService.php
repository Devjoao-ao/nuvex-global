<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityService
{
    public function log(
        User $user,
        string $action,
        string $entityType,
        $entityId,
        ?string $entityName = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
        ?string $ip = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'entity_name' => $entityName,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'ip_address' => $ip,
        ]);
    }

    public function getForEntity(string $entityType, $entityId)
    {
        return ActivityLog::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->with('user')
            ->latest()
            ->get();
    }

    public function getRecent(int $limit = 20)
    {
        return ActivityLog::with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
