<?php

namespace Database\Seeders;

use App\Enums\PlatformRole;
use App\Models\User;
use App\Support\PermissionRegistry;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionRegistry::permissions() as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        foreach (PermissionRegistry::rolePermissions() as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($permissions);
        }

        User::query()
            ->where('platform_role', PlatformRole::SuperAdmin)
            ->each(fn (User $user) => $user->syncRoles(['super_admin']));
    }
}
