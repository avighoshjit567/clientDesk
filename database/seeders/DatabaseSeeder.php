<?php

namespace Database\Seeders;

use App\Enums\PlatformRole;
use App\Models\SubscriptionPlan;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        SubscriptionPlan::query()->upsert([
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Starter plan for small sales teams.',
                'price_monthly' => 29,
                'price_yearly' => 290,
                'max_users' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'description' => 'Growth plan for active sales organizations.',
                'price_monthly' => 79,
                'price_yearly' => 790,
                'max_users' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Scale',
                'slug' => 'scale',
                'description' => 'Scale plan for larger real estate teams.',
                'price_monthly' => 149,
                'price_yearly' => 1490,
                'max_users' => 50,
                'is_active' => true,
            ],
        ], uniqueBy: ['slug'], update: ['name', 'description', 'price_monthly', 'price_yearly', 'max_users', 'is_active']);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'platform_role' => PlatformRole::SuperAdmin,
        ]);

        $user->syncRoles(['super_admin']);
    }
}
