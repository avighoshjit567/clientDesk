<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Building2, CalendarClock, CreditCard, ListChecks, Users, UsersRound } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import StatCard from '@/components/StatCard.vue';
import StatusBadge from '@/components/StatusBadge.vue';

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

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <PageHeader
            eyebrow="Platform control"
            title="Super Admin dashboard"
            description="Monitor tenants, platform usage, and overall CRM activity across the whole ClientDesk installation."
        />

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Tenants" :value="stats.totalTenants" :hint="`${stats.activeTenants} active`" :icon="Building2" />
            <StatCard label="Users" :value="stats.totalUsers" hint="Across all tenants." :icon="Users" />
            <StatCard label="Active plans" :value="stats.activePlans" hint="Subscription plans available." :icon="CreditCard" />
            <StatCard label="Leads" :value="stats.totalLeads" hint="Platform-wide leads." :icon="UsersRound" />
            <StatCard label="Tasks" :value="stats.totalTasks" hint="Open work items." :icon="ListChecks" />
            <StatCard label="Pending follow-ups" :value="stats.pendingFollowUps" hint="Scheduled touchpoints." :icon="CalendarClock" />
        </section>

        <section class="rounded-2xl border border-border bg-card p-6">
            <div>
                <h2 class="text-lg font-semibold">Recent tenants</h2>
                <p class="mt-1 text-sm text-muted-foreground">Latest workspaces created on the platform.</p>
            </div>

            <!-- Mobile cards -->
            <div class="mt-5 space-y-3 md:hidden">
                <div v-for="tenant in recentTenants" :key="tenant.id" class="rounded-xl border border-border p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-medium">{{ tenant.name }}</div>
                            <div class="mt-0.5 text-xs text-muted-foreground">{{ tenant.slug }}</div>
                        </div>
                        <StatusBadge :status="tenant.status" />
                    </div>
                    <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <dt class="text-xs text-muted-foreground">Plan</dt>
                            <dd>{{ tenant.plan || 'None' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted-foreground">Owner</dt>
                            <dd>{{ tenant.owner || 'Unknown' }}</dd>
                        </div>
                    </dl>
                </div>
                <div v-if="recentTenants.length === 0" class="rounded-xl border border-dashed border-border p-6 text-center text-sm text-muted-foreground">
                    No tenants yet. New workspaces will show up here.
                </div>
            </div>

            <!-- Desktop table -->
            <div class="mt-5 hidden overflow-x-auto md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-border text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">Tenant</th>
                            <th class="px-3 py-3 font-medium">Plan</th>
                            <th class="px-3 py-3 font-medium">Owner</th>
                            <th class="px-3 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="tenant in recentTenants"
                            :key="tenant.id"
                            class="border-b border-border/50 transition hover:bg-muted/50"
                        >
                            <td class="px-3 py-3">
                                <div class="font-medium">{{ tenant.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ tenant.slug }}</div>
                            </td>
                            <td class="px-3 py-3">{{ tenant.plan || 'None' }}</td>
                            <td class="px-3 py-3">{{ tenant.owner || 'Unknown' }}</td>
                            <td class="px-3 py-3"><StatusBadge :status="tenant.status" /></td>
                        </tr>
                        <tr v-if="recentTenants.length === 0">
                            <td colspan="4" class="px-3 py-10 text-center text-muted-foreground">
                                No tenants yet. New workspaces will show up here.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
