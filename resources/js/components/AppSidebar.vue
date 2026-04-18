<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    FolderGit2,
    LayoutGrid,
    ListChecks,
    ShieldCheck,
    Sparkles,
    Users,
    UsersRound,
} from 'lucide-vue-next';
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

const formatRole = (value: string | null | undefined) => {
    if (!value) {
        return 'Workspace member';
    }

    return value
        .split('_')
        .map((segment) => segment.charAt(0).toUpperCase() + segment.slice(1))
        .join(' ');
};

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
        items.push(
            {
                title: 'Tenant Admin',
                href: '/tenant-admin/dashboard',
                icon: Users,
            },
            {
                title: 'Team',
                href: '/tenant-admin/team',
                icon: Users,
            },
        );
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

const workspaceLabel = computed(() => {
    if (page.props.auth?.context?.isSuperAdmin) {
        return 'Platform owner';
    }

    return formatRole(page.props.auth?.context?.currentTenantRole);
});

const workspaceCopy = computed(() => {
    if (page.props.auth?.context?.isSuperAdmin) {
        return 'Monitor tenants, workspace growth, and platform activity from one control panel.';
    }

    return 'Track pipeline activity, team execution, and workspace operations from a focused CRM shell.';
});

const permissionSummary = computed(() => {
    const permissionCount = page.props.auth?.context?.permissions?.length ?? 0;

    if (page.props.auth?.context?.isSuperAdmin) {
        return 'Full platform access';
    }

    return `${permissionCount} active permission${permissionCount === 1 ? '' : 's'}`;
});
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="border-r-0 bg-sidebar text-sidebar-foreground"
    >
        <SidebarHeader class="border-b border-white/6 px-3 py-3">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="crm-sidebar-panel h-auto rounded-[24px] p-3 text-sidebar-foreground hover:bg-white/8"
                    >
                        <Link :href="dashboard()" class="flex items-center gap-3">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>

            <div class="crm-sidebar-panel mt-3 rounded-[24px] p-3">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.22em] text-sidebar-foreground/50">
                    <Sparkles class="size-3.5 text-orange-300" />
                    Live workspace
                </div>

                <div class="mt-3 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-sidebar-foreground">
                            {{ workspaceLabel }}
                        </p>
                        <p class="mt-1 text-xs leading-5 text-sidebar-foreground/60">
                            {{ permissionSummary }}
                        </p>
                    </div>

                    <span class="inline-flex size-2.5 rounded-full bg-emerald-400"></span>
                </div>

                <p class="mt-3 text-xs leading-5 text-sidebar-foreground/60">
                    {{ workspaceCopy }}
                </p>
            </div>
        </SidebarHeader>

        <SidebarContent class="px-2 py-4">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="border-t border-white/6 px-3 py-3">
            <div class="crm-sidebar-panel mb-3 rounded-[22px] p-3 text-xs leading-5 text-sidebar-foreground/60">
                Built for sales teams that need clean execution, clear roles, and fast follow-ups.
            </div>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
