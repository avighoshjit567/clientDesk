<?php

namespace Tests\Feature\Leads;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_without_a_workspace_are_redirected_from_leads_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('leads.index'));

        $response->assertRedirect(route('tenants.onboarding.create'));
    }

    public function test_workspace_users_can_view_leads_index(): void
    {
        $user = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($user);
        $source = LeadSource::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Website',
            'slug' => 'website',
            'description' => 'Landing page leads',
            'is_active' => true,
        ]);

        Lead::query()->create([
            'tenant_id' => $tenant->id,
            'lead_source_id' => $source->id,
            'assigned_to_user_id' => $user->id,
            'created_by_user_id' => $user->id,
            'first_name' => 'Rafi',
            'last_name' => 'Ahmed',
            'email' => 'rafi@example.com',
            'primary_phone' => '01700000000',
            'status' => LeadStatus::New,
            'notes' => 'Interested in a two-bedroom flat.',
        ]);

        $response = $this->actingAs($user)->get(route('leads.index'));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('leads/Index')
            ->where('stats.total', 1)
            ->where('stats.new', 1)
            ->has('leadSources', 1)
            ->has('leads', 1)
            ->where('leads.0.name', 'Rafi Ahmed')
            ->where('leads.0.source', 'Website')
        );
    }

    public function test_workspace_users_can_create_lead_sources_scoped_to_their_tenant(): void
    {
        $user = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($user);

        $response = $this->actingAs($user)->post(route('lead-sources.store'), [
            'name' => 'WhatsApp Campaign',
            'description' => 'Inbound WhatsApp responses',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('lead_sources', [
            'tenant_id' => $tenant->id,
            'name' => 'WhatsApp Campaign',
            'slug' => 'whatsapp-campaign',
        ]);
    }

    public function test_workspace_users_can_create_leads_scoped_to_their_tenant(): void
    {
        $owner = User::factory()->create();
        $assignee = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($owner);
        $tenant->users()->attach($assignee->id, [
            'role' => 'sales_rep',
            'is_primary' => false,
            'joined_at' => now(),
            'invited_by_user_id' => $owner->id,
        ]);

        $source = LeadSource::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Referral',
            'slug' => 'referral',
            'description' => 'Friend or client referral',
            'is_active' => true,
        ]);

        $response = $this->actingAs($owner)->post(route('leads.store'), [
            'first_name' => 'Nusrat',
            'last_name' => 'Jahan',
            'email' => 'nusrat@example.com',
            'primary_phone' => '01800000000',
            'status' => LeadStatus::Qualified->value,
            'lead_source_id' => $source->id,
            'assigned_to_user_id' => $assignee->id,
            'notes' => 'Looking for a ready apartment near Gulshan.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'tenant_id' => $tenant->id,
            'created_by_user_id' => $owner->id,
            'assigned_to_user_id' => $assignee->id,
            'lead_source_id' => $source->id,
            'first_name' => 'Nusrat',
            'last_name' => 'Jahan',
            'status' => LeadStatus::Qualified->value,
        ]);
    }

    private function createWorkspaceForUser(User $user): Tenant
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
            'role' => 'tenant_admin',
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
