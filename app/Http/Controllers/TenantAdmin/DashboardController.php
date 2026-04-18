<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\Task;
use App\Models\TenantInvitation;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $tenantId = request()->user()->current_tenant_id;

        $team = User::query()
            ->select('users.id', 'users.name', 'users.email', 'tenant_user.role')
            ->join('tenant_user', 'tenant_user.user_id', '=', 'users.id')
            ->where('tenant_user.tenant_id', $tenantId)
            ->orderBy('users.name')
            ->get();

        return Inertia::render('tenant-admin/Dashboard', [
            'stats' => [
                'teamMembers' => $team->count(),
                'pendingInvitations' => TenantInvitation::query()->where('tenant_id', $tenantId)->whereNull('accepted_at')->count(),
                'totalLeads' => Lead::query()->where('tenant_id', $tenantId)->count(),
                'totalTasks' => Task::query()->where('tenant_id', $tenantId)->count(),
                'pendingFollowUps' => FollowUp::query()->where('tenant_id', $tenantId)->where('status', 'pending')->count(),
            ],
            'team' => $team,
        ]);
    }
}
