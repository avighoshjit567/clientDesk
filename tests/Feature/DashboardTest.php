<?php

namespace Tests\Feature;

use App\Enums\PlatformRole;
use App\Models\Lead;
use App\Models\SubscriptionPlan;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\TenantSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_super_admin_users_are_redirected_to_the_super_admin_dashboard()
    {
        $user = User::factory()->create([
            'platform_role' => PlatformRole::SuperAdmin,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('super-admin.dashboard'));
    }

    public function test_authenticated_users_without_a_workspace_are_redirected_to_onboarding()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('tenants.onboarding.create'));
    }

    public function test_tenant_admin_users_are_redirected_to_the_tenant_admin_dashboard()
    {
        $user = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($user, 'tenant_admin');

        Lead::query()->create([
            'tenant_id' => $tenant->id,
            'created_by_user_id' => $user->id,
            'assigned_to_user_id' => $user->id,
            'first_name' => 'Rafi',
            'primary_phone' => '01700000000',
            'status' => 'new',
        ]);

        Task::query()->create([
            'tenant_id' => $tenant->id,
            'created_by_user_id' => $user->id,
            'assigned_to_user_id' => $user->id,
            'title' => 'Review lead',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('tenant-admin.dashboard'));
    }

    public function test_sales_rep_users_can_visit_the_workspace_dashboard()
    {
        $owner = User::factory()->create();
        $salesRep = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($owner, 'tenant_admin');

        $tenant->users()->attach($salesRep->id, [
            'role' => 'sales_rep',
            'is_primary' => false,
            'joined_at' => now(),
            'invited_by_user_id' => $owner->id,
        ]);

        $salesRep->forceFill([
            'current_tenant_id' => $tenant->id,
        ])->save();

        $this->actingAs($salesRep);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('workspace.name', 'Acme Realty')
            ->where('workspace.slug', 'acme-realty')
            ->where('stats.memberCount', 2)
            ->where('stats.pendingInvitationCount', 0)
        );
    }

    private function createWorkspaceForUser(User $user, string $role): Tenant
    {
        $plan = SubscriptionPlan::query()->create([
            'name' => 'Starter',
            'slug' => 'starter',
            'description' => 'Starter plan',
            'price_monthly' => 29,
            'price_yearly' => 290,
            'max_users' => 5,
            'is_active' => true,
        ]);

        $tenant = Tenant::query()->create([
            'subscription_plan_id' => $plan->id,
            'owner_user_id' => $user->id,
            'name' => 'Acme Realty',
            'slug' => 'acme-realty',
            'status' => 'active',
            'timezone' => 'Asia/Dhaka',
        ]);

        TenantSetting::query()->create([
            'tenant_id' => $tenant->id,
            'currency_code' => 'BDT',
            'phone_country_code' => '+880',
            'default_country' => 'BD',
            'locale' => 'en',
        ]);

        $tenant->users()->attach($user->id, [
            'role' => $role,
            'is_primary' => true,
            'joined_at' => now(),
            'invited_by_user_id' => null,
        ]);

        $user->forceFill([
            'current_tenant_id' => $tenant->id,
        ])->save();

        return $tenant;
    }
}
