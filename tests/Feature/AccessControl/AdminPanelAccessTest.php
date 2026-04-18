<?php

namespace Tests\Feature\AccessControl;

use App\Enums\PlatformRole;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_super_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'platform_role' => PlatformRole::SuperAdmin,
        ]);

        $this->actingAs($user)
            ->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('super-admin/Dashboard'));
    }

    public function test_non_super_admin_cannot_view_super_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('super-admin.dashboard'))
            ->assertForbidden();
    }

    public function test_tenant_admin_can_view_tenant_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $this->createWorkspaceForUser($user, 'tenant_admin');

        $this->actingAs($user)
            ->get(route('tenant-admin.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('tenant-admin/Dashboard'));
    }

    public function test_sales_rep_cannot_view_tenant_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $this->createWorkspaceForUser($user, 'sales_rep');

        $this->actingAs($user)
            ->get(route('tenant-admin.dashboard'))
            ->assertForbidden();
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
            'slug' => 'acme-realty-'.$role,
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
