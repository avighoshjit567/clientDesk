<?php

namespace App\Policies;

use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasTenantPermission('tasks.view');
    }

    public function create(User $user): bool
    {
        return $user->hasTenantPermission('tasks.create');
    }
}
