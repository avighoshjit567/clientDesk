<?php

namespace App\Support;

class PermissionRegistry
{
    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'platform.view',
            'tenants.manage',
            'team.manage',
            'leads.view',
            'leads.create',
            'tasks.view',
            'tasks.create',
            'followups.view',
            'followups.create',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function assignableTenantRoles(): array
    {
        return [
            'tenant_admin',
            'manager',
            'sales_rep',
            'telecaller',
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function rolePermissions(): array
    {
        return [
            'super_admin' => self::permissions(),
            'tenant_admin' => [
                'tenants.manage',
                'team.manage',
                'leads.view',
                'leads.create',
                'tasks.view',
                'tasks.create',
                'followups.view',
                'followups.create',
            ],
            'manager' => [
                'team.manage',
                'leads.view',
                'leads.create',
                'tasks.view',
                'tasks.create',
                'followups.view',
                'followups.create',
            ],
            'sales_rep' => [
                'leads.view',
                'leads.create',
                'tasks.view',
                'tasks.create',
                'followups.view',
                'followups.create',
            ],
            'telecaller' => [
                'leads.view',
                'leads.create',
                'tasks.view',
                'followups.view',
                'followups.create',
            ],
        ];
    }
}
