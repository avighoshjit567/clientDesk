<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Activity,
    BadgeCheck,
    Building2,
    CreditCard,
    ListChecks,
    Target,
    Users,
} from 'lucide-vue-next';

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

const props = defineProps<{
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

    <div class="crm-page">
        <section class="crm-hero">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="crm-eyebrow">Platform control</p>
                    <h1 class="crm-title">Super Admin command center</h1>
                    <p class="crm-subtitle">
                        Track workspace growth, plan adoption, and operational health across the full ClientDesk platform.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="crm-chip crm-chip-success">{{ props.stats.activeTenants }} active workspaces</span>
                        <span class="crm-chip crm-chip-muted">{{ props.stats.totalUsers }} total users</span>
                        <span class="crm-chip crm-chip-muted">{{ props.stats.activePlans }} plans in use</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:w-[360px]">
                    <div class="crm-card-soft">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-muted-foreground">Adoption</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight text-foreground">{{ props.stats.totalTenants }}</p>
                        <p class="mt-2 text-sm text-muted-foreground">Total tenant workspaces created so far.</p>
                    </div>
                    <div class="crm-card-soft">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-muted-foreground">Activity</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight text-foreground">{{ props.stats.pendingFollowUps }}</p>
                        <p class="mt-2 text-sm text-muted-foreground">Follow-ups still pending across the platform.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="crm-stat-grid xl:grid-cols-4">
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><Building2 class="size-5" /></div>
                <p class="crm-stat-label">Tenants</p>
                <p class="crm-stat-value">{{ props.stats.totalTenants }}</p>
                <p class="crm-stat-caption">Every company workspace currently provisioned.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><BadgeCheck class="size-5" /></div>
                <p class="crm-stat-label">Active tenants</p>
                <p class="crm-stat-value">{{ props.stats.activeTenants }}</p>
                <p class="crm-stat-caption">Workspaces actively moving through the CRM.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><Users class="size-5" /></div>
                <p class="crm-stat-label">Users</p>
                <p class="crm-stat-value">{{ props.stats.totalUsers }}</p>
                <p class="crm-stat-caption">Platform-wide user count across all tenants.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><CreditCard class="size-5" /></div>
                <p class="crm-stat-label">Active plans</p>
                <p class="crm-stat-value">{{ props.stats.activePlans }}</p>
                <p class="crm-stat-caption">Billing plans currently assigned to workspaces.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><Target class="size-5" /></div>
                <p class="crm-stat-label">Leads</p>
                <p class="crm-stat-value">{{ props.stats.totalLeads }}</p>
                <p class="crm-stat-caption">Total lead volume stored across tenants.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><ListChecks class="size-5" /></div>
                <p class="crm-stat-label">Tasks</p>
                <p class="crm-stat-value">{{ props.stats.totalTasks }}</p>
                <p class="crm-stat-caption">Execution workload currently being tracked.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><Activity class="size-5" /></div>
                <p class="crm-stat-label">Pending follow-ups</p>
                <p class="crm-stat-value">{{ props.stats.pendingFollowUps }}</p>
                <p class="crm-stat-caption">Upcoming follow-ups that still need action.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <article class="crm-card">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="crm-panel-title">Recent tenants</h2>
                        <p class="crm-panel-copy">A quick read on the newest workspaces added to the platform.</p>
                    </div>
                </div>

                <div class="crm-table-wrap">
                    <table class="crm-table">
                        <thead>
                            <tr>
                                <th>Tenant</th>
                                <th>Plan</th>
                                <th>Owner</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="tenant in props.recentTenants" :key="tenant.id">
                                <td>
                                    <div class="font-medium text-foreground">{{ tenant.name }}</div>
                                    <div class="mt-1 text-xs text-muted-foreground">{{ tenant.slug }}</div>
                                </td>
                                <td>{{ tenant.plan || 'No plan' }}</td>
                                <td>{{ tenant.owner || 'Unknown owner' }}</td>
                                <td>
                                    <span class="crm-chip" :class="tenant.status === 'active' ? 'crm-chip-success' : 'crm-chip-warning'">
                                        {{ tenant.status }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="props.recentTenants.length === 0">
                                <td colspan="4">
                                    <div class="crm-empty-state">No tenant activity yet.</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="crm-card">
                <h2 class="crm-panel-title">Platform watchlist</h2>
                <p class="crm-panel-copy">Useful signals to keep this SaaS stable as it grows.</p>

                <div class="mt-5 space-y-3">
                    <div class="crm-list-dot">Keep an eye on activation rate between created tenants and active tenants.</div>
                    <div class="crm-list-dot">Track whether lead volume is turning into task and follow-up activity.</div>
                    <div class="crm-list-dot">Review plan adoption before adding more complex billing flows.</div>
                </div>
            </article>
        </section>
    </div>
</template>
