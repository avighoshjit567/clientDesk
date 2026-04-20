<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BellDot, CalendarClock, Search, ShieldCheck, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { Auth, BreadcrumbItem } from '@/types';

const props = withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

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

const roleLabel = computed(() => {
    if (page.props.auth?.context?.isSuperAdmin) {
        return 'Super Admin';
    }

    return formatRole(page.props.auth?.context?.currentTenantRole);
});

const greeting = computed(() => {
    const name = page.props.auth?.user?.name;

    if (!name) {
        return 'Welcome back';
    }

    return `Good to see you, ${name.split(' ')[0]}`;
});

const primaryAction = computed(() => {
    const permissions = page.props.auth?.context?.permissions ?? [];

    if (permissions.includes('leads.view')) {
        return { label: 'Open leads', href: '/leads' };
    }

    if (permissions.includes('tasks.view')) {
        return { label: 'Open tasks', href: '/tasks' };
    }

    return { label: 'View team', href: '/tenant-admin/team' };
});
</script>

<template>
    <header class="sticky top-0 z-10 border-b border-border/50 bg-background/80 px-4 py-4 backdrop-blur-xl md:px-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex min-w-0 items-start gap-3">
                <SidebarTrigger class="mt-0.5 rounded-xl border border-border/60 bg-card/70 p-2 shadow-sm hover:bg-accent md:mt-1" />

                <div class="min-w-0">
                    <p class="text-sm font-medium text-muted-foreground">
                        {{ greeting }}
                    </p>

                    <template v-if="props.breadcrumbs && props.breadcrumbs.length > 0">
                        <div class="mt-1">
                            <Breadcrumbs :breadcrumbs="breadcrumbs" />
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex flex-1 flex-col gap-3 xl:max-w-3xl xl:flex-row xl:items-center xl:justify-end">
                <div class="flex min-w-0 items-center gap-3 rounded-2xl border border-border/60 bg-card/70 px-4 py-3 shadow-sm xl:min-w-[320px] xl:flex-1">
                    <Search class="size-4 text-muted-foreground" />
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-foreground">Search leads, tasks, or teammates</p>
                        <p class="truncate text-xs text-muted-foreground">Command-style search surface, coming next</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 xl:justify-end">
                    <Link :href="primaryAction.href" class="crm-button-primary h-11 px-4 py-0">
                        <Sparkles class="size-4" />
                        {{ primaryAction.label }}
                    </Link>
                    <span class="crm-chip crm-chip-muted h-11 px-4">
                        <ShieldCheck class="size-3.5" />
                        {{ roleLabel }}
                    </span>
                    <span class="crm-chip crm-chip-success h-11 px-4">
                        <BellDot class="size-3.5" />
                        Workspace live
                    </span>
                    <span class="crm-chip crm-chip-muted h-11 px-4">
                        <CalendarClock class="size-3.5" />
                        Daily focus
                    </span>
                </div>
            </div>
        </div>
    </header>
</template>
