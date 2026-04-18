<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Task;
use App\Models\TenantInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response|RedirectResponse
    {
        $user = request()->user();

        if ($user?->isSuperAdmin()) {
            return Redirect::route('super-admin.dashboard');
        }

        $tenant = $user?->currentTenant?->loadMissing(['subscriptionPlan', 'settings']);

        if ($tenant === null) {
            return Redirect::route('tenants.onboarding.create');
        }

        if ($user->hasCurrentTenantRole(['tenant_admin', 'manager'])) {
            return Redirect::route('tenant-admin.dashboard');
        }

        return Inertia::render('Dashboard', [
            'workspace' => [
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'status' => $tenant->status,
                'timezone' => $tenant->timezone,
                'plan' => $tenant->subscriptionPlan?->name,
                'currencyCode' => $tenant->settings?->currency_code,
                'phoneCountryCode' => $tenant->settings?->phone_country_code,
            ],
            'stats' => [
                'memberCount' => $tenant->users()->count(),
                'pendingInvitationCount' => TenantInvitation::query()->where('tenant_id', $tenant->id)->whereNull('accepted_at')->count(),
                'leadCount' => Lead::query()->where('tenant_id', $tenant->id)->count(),
                'taskCount' => Task::query()->where('tenant_id', $tenant->id)->count(),
            ],
        ]);
    }
}
