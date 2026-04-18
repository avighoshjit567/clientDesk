<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\SubscriptionPlan;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('super-admin/Dashboard', [
            'stats' => [
                'totalTenants' => Tenant::query()->count(),
                'activeTenants' => Tenant::query()->where('status', 'active')->count(),
                'totalUsers' => User::query()->count(),
                'totalLeads' => Lead::query()->count(),
                'totalTasks' => Task::query()->count(),
                'pendingFollowUps' => FollowUp::query()->where('status', 'pending')->count(),
                'activePlans' => SubscriptionPlan::query()->where('is_active', true)->count(),
            ],
            'recentTenants' => Tenant::query()
                ->with(['subscriptionPlan:id,name', 'owner:id,name'])
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn (Tenant $tenant) => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'slug' => $tenant->slug,
                    'status' => $tenant->status,
                    'plan' => $tenant->subscriptionPlan?->name,
                    'owner' => $tenant->owner?->name,
                ]),
        ]);
    }
}
