<?php

namespace Tests\Feature\Tasks;

use App\Enums\FollowUpStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\SubscriptionPlan;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\TenantSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_without_a_workspace_are_redirected_from_tasks_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertRedirect(route('tenants.onboarding.create'));
    }

    public function test_workspace_users_can_view_tasks_index(): void
    {
        $user = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($user);
        $lead = $this->createLeadForTenant($tenant, $user);

        Task::query()->create([
            'tenant_id' => $tenant->id,
            'lead_id' => $lead->id,
            'assigned_to_user_id' => $user->id,
            'created_by_user_id' => $user->id,
            'title' => 'Call Rafi about unit availability',
            'description' => 'Discuss preferred budget and location.',
            'status' => TaskStatus::Pending,
            'priority' => TaskPriority::High,
            'due_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('tasks/Index')
            ->where('stats.totalTasks', 1)
            ->where('stats.pendingTasks', 1)
            ->has('tasks', 1)
            ->where('tasks.0.title', 'Call Rafi about unit availability')
        );
    }

    public function test_workspace_users_can_create_tasks_scoped_to_their_tenant(): void
    {
        $owner = User::factory()->create();
        $assignee = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($owner);
        $lead = $this->createLeadForTenant($tenant, $owner);

        $tenant->users()->attach($assignee->id, [
            'role' => 'sales_rep',
            'is_primary' => false,
            'joined_at' => now(),
            'invited_by_user_id' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->post(route('tasks.store'), [
            'title' => 'Book site visit',
            'description' => 'Schedule a visit for Saturday afternoon.',
            'status' => TaskStatus::InProgress->value,
            'priority' => TaskPriority::Urgent->value,
            'due_at' => now()->addDays(2)->toDateTimeString(),
            'lead_id' => $lead->id,
            'assigned_to_user_id' => $assignee->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'tenant_id' => $tenant->id,
            'lead_id' => $lead->id,
            'assigned_to_user_id' => $assignee->id,
            'created_by_user_id' => $owner->id,
            'title' => 'Book site visit',
            'status' => TaskStatus::InProgress->value,
            'priority' => TaskPriority::Urgent->value,
        ]);
    }

    public function test_workspace_users_can_create_follow_ups_scoped_to_their_tenant(): void
    {
        $owner = User::factory()->create();
        $assignee = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($owner);
        $lead = $this->createLeadForTenant($tenant, $owner);

        $tenant->users()->attach($assignee->id, [
            'role' => 'sales_rep',
            'is_primary' => false,
            'joined_at' => now(),
            'invited_by_user_id' => $owner->id,
        ]);

        $task = Task::query()->create([
            'tenant_id' => $tenant->id,
            'lead_id' => $lead->id,
            'assigned_to_user_id' => $assignee->id,
            'created_by_user_id' => $owner->id,
            'title' => 'Prepare pricing options',
            'status' => TaskStatus::Pending,
            'priority' => TaskPriority::Medium,
        ]);

        $response = $this->actingAs($owner)->post(route('follow-ups.store'), [
            'scheduled_for' => now()->addDay()->toDateTimeString(),
            'status' => FollowUpStatus::Pending->value,
            'lead_id' => $lead->id,
            'task_id' => $task->id,
            'assigned_to_user_id' => $assignee->id,
            'notes' => 'Share updated pricing and confirm interest.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('follow_ups', [
            'tenant_id' => $tenant->id,
            'lead_id' => $lead->id,
            'task_id' => $task->id,
            'assigned_to_user_id' => $assignee->id,
            'created_by_user_id' => $owner->id,
            'status' => FollowUpStatus::Pending->value,
        ]);
    }

    public function test_telecallers_cannot_create_tasks(): void
    {
        $user = User::factory()->create();
        $tenant = $this->createWorkspaceForUser($user);
        $lead = $this->createLeadForTenant($tenant, $user);

        $tenant->users()->updateExistingPivot($user->id, [
            'role' => 'telecaller',
        ]);

        $this->actingAs($user)
            ->post(route('tasks.store'), [
                'title' => 'This should fail',
                'status' => TaskStatus::Pending->value,
                'priority' => TaskPriority::Medium->value,
                'lead_id' => $lead->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('tasks', [
            'tenant_id' => $tenant->id,
            'title' => 'This should fail',
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

    private function createLeadForTenant(Tenant $tenant, User $user): Lead
    {
        $source = LeadSource::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Website',
            'slug' => 'website',
            'description' => 'Landing page leads',
            'is_active' => true,
        ]);

        return Lead::query()->create([
            'tenant_id' => $tenant->id,
            'lead_source_id' => $source->id,
            'assigned_to_user_id' => $user->id,
            'created_by_user_id' => $user->id,
            'first_name' => 'Rafi',
            'last_name' => 'Ahmed',
            'email' => 'rafi@example.com',
            'primary_phone' => '01700000000',
            'status' => 'new',
        ]);
    }
}
