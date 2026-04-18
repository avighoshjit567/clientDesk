<?php

namespace App\Http\Controllers\Tenants;

use App\Actions\Tenants\CreateTenantWorkspace;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\StoreTenantRequest;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class TenantOnboardingController extends Controller
{
    public function create(): Response|RedirectResponse
    {
        if (request()->user()?->current_tenant_id) {
            return Redirect::route('dashboard');
        }

        return Inertia::render('onboarding/TenantCreate', [
            'plans' => SubscriptionPlan::query()
                ->where('is_active', true)
                ->orderBy('price_monthly')
                ->get(['name', 'slug', 'description', 'price_monthly', 'price_yearly', 'max_users']),
            'defaults' => [
                'timezone' => 'Asia/Dhaka',
                'currencyCode' => 'BDT',
                'phoneCountryCode' => '+880',
                'defaultCountry' => 'BD',
            ],
        ]);
    }

    public function store(StoreTenantRequest $request, CreateTenantWorkspace $createTenantWorkspace): RedirectResponse
    {
        if ($request->user()?->current_tenant_id) {
            return Redirect::route('dashboard');
        }

        $tenant = $createTenantWorkspace->handle($request->user(), $request->validated());

        return Redirect::route('dashboard')->with('success', "Workspace {$tenant->name} created successfully.");
    }
}
