<?php

namespace App\Policies;

use App\Models\User;

class LeadSourcePolicy
{
    public function create(User $user): bool
    {
        return $user->hasTenantPermission('leads.create');
    }
}
