<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Super Admin',
                href: '/super-admin/dashboard',
            },
        ],
    },
});

defineProps<{
    stats: {
        totalTenants: number;
        activeTenants: number;
        totalUsers: number;
        totalLeads: number;
        totalTasks: number;
        pendingFollowUps: number;
        activePlans: number;
    };
    recentTenants: Array<{
        id: number;
        name: string;
        slug: string;
        status: string;
        plan: string | null;
        owner: string | null;
    }>;
}>();
</script>

<template>
    <Head title="Super Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
        <section class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <p class="text-sm font-medium text-orange-500">Platform control</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Super Admin dashboard</h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 text-muted-foreground">
                Monitor tenants, platform usage, and overall CRM activity across the whole ClientDesk installation.
            </p>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Tenants</p><p class="mt-2 text-3xl font-semibold">{{ stats.totalTenants }}</p></div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Active tenants</p><p class="mt-2 text-3xl font-semibold">{{ stats.activeTenants }}</p></div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Users</p><p class="mt-2 text-3xl font-semibold">{{ stats.totalUsers }}</p></div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Active plans</p><p class="mt-2 text-3xl font-semibold">{{ stats.activePlans }}</p></div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Leads</p><p class="mt-2 text-3xl font-semibold">{{ stats.totalLeads }}</p></div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Tasks</p><p class="mt-2 text-3xl font-semibold">{{ stats.totalTasks }}</p></div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Pending follow-ups</p><p class="mt-2 text-3xl font-semibold">{{ stats.pendingFollowUps }}</p></div>
        </section>

        <section class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <h2 class="text-lg font-semibold">Recent tenants</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border">
                        <tr>
                            <th class="px-3 py-3 font-medium">Tenant</th>
                            <th class="px-3 py-3 font-medium">Plan</th>
                            <th class="px-3 py-3 font-medium">Owner</th>
                            <th class="px-3 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="tenant in recentTenants" :key="tenant.id" class="border-b border-sidebar-border/40 dark:border-sidebar-border/60">
                            <td class="px-3 py-3"><div class="font-medium">{{ tenant.name }}</div><div class="text-xs text-muted-foreground">{{ tenant.slug }}</div></td>
                            <td class="px-3 py-3">{{ tenant.plan || 'None' }}</td>
                            <td class="px-3 py-3">{{ tenant.owner || 'Unknown' }}</td>
                            <td class="px-3 py-3">{{ tenant.status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
