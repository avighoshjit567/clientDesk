<?php

namespace App\Policies;

use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasTenantPermission('leads.view');
    }

    public function create(User $user): bool
    {
        return $user->hasTenantPermission('leads.create');
    }
}
