<?php

namespace Tests\Feature\TenantAdmin;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantInvitation;
use App\Models\TenantSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TeamManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_admin_can_view_team_management_page(): void
    {
        $user = User::factory()->create();
        $this->createWorkspaceForUser($user, 'tenant_admin');

        $this->actingAs($user)
            ->get(route('tenant-admin.team.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tenant-admin/Team')
                ->where('workspace.name', 'Acme Realty')
                ->has('team', 1)
            );
    }

    public function test_sales_rep_cannot_access_team_management(): void
    {
        $user = User::factory()->create();
        $this->createWorkspaceForUser($user, 'sales_rep');

        $this->actingAs($user)
            ->get(route('tenant-admin.team.index'))
            ->assertForbidden();
    }

    public function test_tenant_admin_can_create_invitation(): void
    {
        $owner = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($owner, 'tenant_admin');

        $this->actingAs($owner)
            ->post(route('tenant-admin.invitations.store'), [
                'email' => 'teammate@example.com',
                'role' => 'sales_rep',
            ])
            ->assertRedirect(route('tenant-admin.team.index'));

        $this->assertDatabaseHas('tenant_invitations', [
            'tenant_id' => $tenant->id,
            'email' => 'teammate@example.com',
            'role' => 'sales_rep',
        ]);
    }

    public function test_invited_user_can_accept_workspace_invitation(): void
    {
        $owner = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($owner, 'tenant_admin');
        $invitedUser = User::factory()->create([
            'email' => 'teammate@example.com',
        ]);

        $invitation = TenantInvitation::query()->create([
            'tenant_id' => $tenant->id,
            'email' => 'teammate@example.com',
            'role' => 'sales_rep',
            'token' => 'invite-token-123',
            'invited_by_user_id' => $owner->id,
            'expires_at' => now()->addDays(7),
        ]);

        $this->actingAs($invitedUser)
            ->get(route('tenant-invitations.accept.show', $invitation->token))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('tenant-invitations/Accept'));

        $this->actingAs($invitedUser)
            ->post(route('tenant-invitations.accept.store', $invitation->token))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tenant_user', [
            'tenant_id' => $tenant->id,
            'user_id' => $invitedUser->id,
            'role' => 'sales_rep',
        ]);

        $this->assertDatabaseMissing('tenant_invitations', [
            'id' => $invitation->id,
        ]);

        $this->assertSame($tenant->id, $invitedUser->fresh()->current_tenant_id);
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
