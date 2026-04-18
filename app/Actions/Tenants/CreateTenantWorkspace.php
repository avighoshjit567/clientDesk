<?php

namespace App\Actions\Tenants;

use App\Models\LeadSource;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTenantWorkspace
{
    public function handle(User $user, array $data): Tenant
    {
        return DB::transaction(function () use ($user, $data) {
            $plan = SubscriptionPlan::query()
                ->where('slug', $data['subscription_plan_slug'])
                ->where('is_active', true)
                ->firstOrFail();

            $tenant = Tenant::query()->create([
                'subscription_plan_id' => $plan->id,
                'owner_user_id' => $user->id,
                'name' => $data['company_name'],
                'slug' => $this->generateUniqueSlug($data['company_name']),
                'status' => 'active',
                'timezone' => $data['timezone'],
                'trial_ends_at' => now()->addDays(14),
            ]);

            TenantSetting::query()->create([
                'tenant_id' => $tenant->id,
                'currency_code' => $data['currency_code'],
                'phone_country_code' => $data['phone_country_code'],
                'default_country' => $data['default_country'],
                'locale' => 'en',
            ]);

            $tenant->users()->attach($user->id, [
                'role' => 'tenant_admin',
                'is_primary' => true,
                'joined_at' => now(),
                'invited_by_user_id' => null,
            ]);

            LeadSource::query()->insert([
                [
                    'tenant_id' => $tenant->id,
                    'name' => 'Website',
                    'slug' => 'website',
                    'description' => 'Leads coming from the website or landing pages.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'tenant_id' => $tenant->id,
                    'name' => 'Facebook',
                    'slug' => 'facebook',
                    'description' => 'Leads coming from Facebook ads or messages.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'tenant_id' => $tenant->id,
                    'name' => 'Referral',
                    'slug' => 'referral',
                    'description' => 'Referral or word-of-mouth leads.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $user->forceFill([
                'current_tenant_id' => $tenant->id,
            ])->save();

            return $tenant;
        });
    }

    protected function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $normalizedBaseSlug = $baseSlug !== '' ? $baseSlug : 'workspace';
        $slug = $normalizedBaseSlug;
        $counter = 2;

        while (Tenant::query()->where('slug', $slug)->exists()) {
            $slug = $normalizedBaseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
