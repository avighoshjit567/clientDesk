<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, LayoutGrid, ListChecks, ShieldCheck, Users, UsersRound } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { Auth, NavItem } from '@/types';

const page = usePage<{ auth: Auth }>();

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    const permissions = page.props.auth?.context?.permissions ?? [];
    const currentTenantRole = page.props.auth?.context?.currentTenantRole ?? '';

    if (page.props.auth?.context?.isSuperAdmin) {
        items.push({
            title: 'Super Admin',
            href: '/super-admin/dashboard',
            icon: ShieldCheck,
        });

        return items;
    }

    if (['tenant_admin', 'manager'].includes(currentTenantRole)) {
        items.push({
            title: 'Tenant Admin',
            href: '/tenant-admin/dashboard',
            icon: Users,
        });

        items.push({
            title: 'Team',
            href: '/tenant-admin/team',
            icon: Users,
        });
    }

    if (permissions.includes('leads.view')) {
        items.push({
            title: 'Leads',
            href: '/leads',
            icon: UsersRound,
        });
    }

    if (permissions.includes('tasks.view')) {
        items.push({
            title: 'Tasks',
            href: '/tasks',
            icon: ListChecks,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/avighoshjit567/clientDesk',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
