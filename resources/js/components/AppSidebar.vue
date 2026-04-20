<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Gauge,
    ListChecks,
    Plus,
    Settings,
    ShieldCheck,
    Sparkles,
    Users,
    UsersRound,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { dashboard } from '@/routes';
import type { Auth, NavItem } from '@/types';

const page = usePage<{ auth: Auth }>();
const { isCurrentUrl } = useCurrentUrl();

const formatRole = (value: string | null | undefined) => {
    if (!value) {
        return 'Workspace member';
    }

    return value
        .split('_')
        .map((segment) => segment.charAt(0).toUpperCase() + segment.slice(1))
        .join(' ');
};

const overviewItems = computed<NavItem[]>(() => [
    {
        title: 'Overview',
        href: dashboard(),
        icon: Gauge,
    },
]);

const salesItems = computed<NavItem[]>(() => {
    const permissions = page.props.auth?.context?.permissions ?? [];
    const items: NavItem[] = [];

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

const adminItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];
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
                icon: ShieldCheck,
            },
            {
                title: 'Team',
                href: '/tenant-admin/team',
                icon: Users,
            },
        );
    }

    return items;
});

const personalItems: NavItem[] = [
    {
        title: 'Settings',
        href: '/settings/profile',
        icon: Settings,
    },
];

const workspaceLabel = computed(() => {
    if (page.props.auth?.context?.isSuperAdmin) {
        return 'Platform owner';
    }

    return formatRole(page.props.auth?.context?.currentTenantRole);
});

const shellCopy = computed(() => {
    if (page.props.auth?.context?.isSuperAdmin) {
        return 'Modeled after modern CRM command centers: clearer sections, less starter-kit noise, faster admin access.';
    }

    return 'Restructured toward modern CRM patterns: activity-first navigation, cleaner sales sections, and less visual clutter.';
});

const quickAction = computed(() => {
    if ((page.props.auth?.context?.permissions ?? []).includes('leads.view')) {
        return {
            label: 'Open leads',
            href: '/leads',
        };
    }

    if ((page.props.auth?.context?.permissions ?? []).includes('tasks.view')) {
        return {
            label: 'Open tasks',
            href: '/tasks',
        };
    }

    return {
        label: 'View overview',
        href: dashboard(),
    };
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-r-0 bg-sidebar text-sidebar-foreground">
        <SidebarHeader class="border-b border-white/6 px-3 py-3">
            <Link :href="dashboard()" class="crm-sidebar-panel flex items-center rounded-[24px] px-3 py-3 text-sidebar-foreground">
                <AppLogo />
            </Link>

            <div class="crm-sidebar-panel mt-3 rounded-[24px] p-4">
                <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-sidebar-foreground/48">
                    <Sparkles class="size-3.5 text-orange-300" />
                    CRM workspace
                </div>

                <div class="mt-3">
                    <p class="text-sm font-semibold text-sidebar-foreground">
                        {{ workspaceLabel }}
                    </p>
                    <p class="mt-2 text-xs leading-5 text-sidebar-foreground/62">
                        {{ shellCopy }}
                    </p>
                </div>

                <Link :href="quickAction.href" class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-orange-400 px-3 py-2 text-xs font-semibold text-slate-950 transition hover:bg-orange-300">
                    <Plus class="size-3.5" />
                    {{ quickAction.label }}
                </Link>
            </div>
        </SidebarHeader>

        <SidebarContent class="px-2 py-4">
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel class="px-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-sidebar-foreground/45">
                    Overview
                </SidebarGroupLabel>
                <SidebarGroupContent>
                    <SidebarMenu class="mt-2 gap-1">
                        <SidebarMenuItem v-for="item in overviewItems" :key="item.title">
                            <SidebarMenuButton
                                as-child
                                :is-active="isCurrentUrl(item.href)"
                                :tooltip="item.title"
                                class="rounded-2xl px-3 py-2.5 text-sidebar-foreground/72 transition hover:bg-white/7 hover:text-sidebar-foreground data-[active=true]:bg-white/8 data-[active=true]:text-sidebar-foreground"
                            >
                                <Link :href="item.href">
                                    <component :is="item.icon" class="size-4" />
                                    <span class="font-medium">{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>

            <SidebarGroup v-if="salesItems.length > 0" class="mt-4 px-2 py-0">
                <SidebarGroupLabel class="px-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-sidebar-foreground/45">
                    Sales
                </SidebarGroupLabel>
                <SidebarGroupContent>
                    <SidebarMenu class="mt-2 gap-1">
                        <SidebarMenuItem v-for="item in salesItems" :key="item.title">
                            <SidebarMenuButton
                                as-child
                                :is-active="isCurrentUrl(item.href)"
                                :tooltip="item.title"
                                class="rounded-2xl px-3 py-2.5 text-sidebar-foreground/72 transition hover:bg-white/7 hover:text-sidebar-foreground data-[active=true]:bg-white/8 data-[active=true]:text-sidebar-foreground"
                            >
                                <Link :href="item.href">
                                    <component :is="item.icon" class="size-4" />
                                    <span class="font-medium">{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>

            <SidebarGroup v-if="adminItems.length > 0" class="mt-4 px-2 py-0">
                <SidebarGroupLabel class="px-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-sidebar-foreground/45">
                    Administration
                </SidebarGroupLabel>
                <SidebarGroupContent>
                    <SidebarMenu class="mt-2 gap-1">
                        <SidebarMenuItem v-for="item in adminItems" :key="item.title">
                            <SidebarMenuButton
                                as-child
                                :is-active="isCurrentUrl(item.href)"
                                :tooltip="item.title"
                                class="rounded-2xl px-3 py-2.5 text-sidebar-foreground/72 transition hover:bg-white/7 hover:text-sidebar-foreground data-[active=true]:bg-white/8 data-[active=true]:text-sidebar-foreground"
                            >
                                <Link :href="item.href">
                                    <component :is="item.icon" class="size-4" />
                                    <span class="font-medium">{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>

            <SidebarGroup class="mt-4 px-2 py-0">
                <SidebarGroupLabel class="px-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-sidebar-foreground/45">
                    Personal
                </SidebarGroupLabel>
                <SidebarGroupContent>
                    <SidebarMenu class="mt-2 gap-1">
                        <SidebarMenuItem v-for="item in personalItems" :key="item.title">
                            <SidebarMenuButton
                                as-child
                                :is-active="isCurrentUrl(item.href)"
                                :tooltip="item.title"
                                class="rounded-2xl px-3 py-2.5 text-sidebar-foreground/72 transition hover:bg-white/7 hover:text-sidebar-foreground data-[active=true]:bg-white/8 data-[active=true]:text-sidebar-foreground"
                            >
                                <Link :href="item.href">
                                    <component :is="item.icon" class="size-4" />
                                    <span class="font-medium">{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter class="border-t border-white/6 px-3 py-3">
            <div class="crm-sidebar-panel mb-3 rounded-[22px] p-3 text-xs leading-5 text-sidebar-foreground/60">
                Inspired by stronger CRM patterns: action-first navigation, sales-focus sections, and product-facing utility instead of dev-facing links.
            </div>
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
