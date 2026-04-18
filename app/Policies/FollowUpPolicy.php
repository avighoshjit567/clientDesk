<?php

namespace App\Policies;

use App\Models\User;

class FollowUpPolicy
{
    public function create(User $user): bool
    {
        return $user->hasTenantPermission('followups.create');
    }
}
