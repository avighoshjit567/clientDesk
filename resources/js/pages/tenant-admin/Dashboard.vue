<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    CalendarClock,
    CheckCircle2,
    ListChecks,
    ShieldCheck,
    Target,
    UserPlus,
    Users,
} from 'lucide-vue-next';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tenant Admin',
                href: '/tenant-admin/dashboard',
            },
        ],
    },
});

const props = defineProps<{
    stats: {
        teamMembers: number;
        pendingInvitations: number;
        totalLeads: number;
        totalTasks: number;
        pendingFollowUps: number;
    };
    team: Array<{
        id: number;
        name: string;
        email: string;
        role: string;
    }>;
}>();

const formatRole = (value: string) =>
    value
        .split('_')
        .map((segment) => segment.charAt(0).toUpperCase() + segment.slice(1))
        .join(' ');

const adminQueue = [
    {
        title: 'Resolve invitation backlog',
        value: props.stats.pendingInvitations,
        description: 'Finish onboarding teammates so access and permissions are not delayed.',
        href: '/tenant-admin/team',
        icon: UserPlus,
    },
    {
        title: 'Review task load',
        value: props.stats.totalTasks,
        description: 'Use task volume to spot whether the team is actually moving leads forward.',
        href: '/tasks',
        icon: ListChecks,
    },
    {
        title: 'Check follow-up pressure',
        value: props.stats.pendingFollowUps,
        description: 'Pending follow-ups are the first sign of pipeline slippage.',
        href: '/tasks',
        icon: CalendarClock,
    },
];
</script>

<template>
    <Head title="Tenant Admin" />

    <div class="crm-page">
        <section class="crm-hero">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-3xl">
                    <p class="crm-eyebrow">Tenant operations</p>
                    <h1 class="crm-title">Tenant Admin command view</h1>
                    <p class="crm-subtitle">
                        This page is now structured more like a workspace operations center, not a starter dashboard. The goal is clearer team control, ownership, and sales execution.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="crm-chip crm-chip-success">{{ props.stats.teamMembers }} team members</span>
                        <span class="crm-chip crm-chip-muted">{{ props.stats.pendingInvitations }} invites pending</span>
                        <span class="crm-chip crm-chip-muted">{{ props.stats.pendingFollowUps }} follow-ups due</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:w-[380px]">
                    <Link href="/tenant-admin/team" class="crm-button-primary h-12">
                        Open team panel
                        <ArrowRight class="size-4" />
                    </Link>
                    <Link href="/tasks" class="crm-button-secondary h-12">Open tasks</Link>
                    <div class="crm-card-soft sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-muted-foreground">Admin note</p>
                        <p class="mt-3 text-sm leading-6 text-muted-foreground">
                            Strong CRM admin views prioritize operational queues over generic dashboard cards.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="crm-stat-grid xl:grid-cols-5">
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><Users class="size-5" /></div>
                <p class="crm-stat-label">Team members</p>
                <p class="crm-stat-value">{{ props.stats.teamMembers }}</p>
                <p class="crm-stat-caption">People already operating inside this tenant workspace.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><UserPlus class="size-5" /></div>
                <p class="crm-stat-label">Invitation queue</p>
                <p class="crm-stat-value">{{ props.stats.pendingInvitations }}</p>
                <p class="crm-stat-caption">Outstanding invites still blocking full rollout.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><Target class="size-5" /></div>
                <p class="crm-stat-label">Lead volume</p>
                <p class="crm-stat-value">{{ props.stats.totalLeads }}</p>
                <p class="crm-stat-caption">Leads already captured for the tenant sales flow.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><ListChecks class="size-5" /></div>
                <p class="crm-stat-label">Task load</p>
                <p class="crm-stat-value">{{ props.stats.totalTasks }}</p>
                <p class="crm-stat-caption">The current execution backlog assigned to the team.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><CalendarClock class="size-5" /></div>
                <p class="crm-stat-label">Follow-ups due</p>
                <p class="crm-stat-value">{{ props.stats.pendingFollowUps }}</p>
                <p class="crm-stat-caption">Customer touchpoints that still need action.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <article class="crm-card">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="crm-panel-title">Admin queue</h2>
                        <p class="crm-panel-copy">A more useful tenant-admin surface, inspired by action-oriented CRM dashboards.</p>
                    </div>
                    <ShieldCheck class="size-5 text-orange-500" />
                </div>

                <div class="mt-5 space-y-4">
                    <Link
                        v-for="row in adminQueue"
                        :key="row.title"
                        :href="row.href"
                        class="block rounded-2xl border border-border/60 bg-background/70 p-4 transition hover:border-orange-300 hover:bg-background"
                    >
                        <div class="flex items-start gap-4">
                            <div class="crm-stat-icon size-12 shrink-0">
                                <component :is="row.icon" class="size-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-foreground">{{ row.title }}</p>
                                    <span class="crm-chip crm-chip-muted">{{ row.value }}</span>
                                </div>
                                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                    {{ row.description }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>
            </article>

            <article class="crm-card">
                <h2 class="crm-panel-title">Team pulse</h2>
                <p class="crm-panel-copy">A lightweight roster rail, similar to what good CRM admin views keep nearby.</p>

                <div class="mt-5 space-y-3">
                    <div v-for="member in props.team.slice(0, 4)" :key="member.id" class="crm-card-soft">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-foreground">{{ member.name }}</p>
                                <p class="mt-1 text-sm text-muted-foreground">{{ member.email }}</p>
                            </div>
                            <span class="crm-chip crm-chip-muted">{{ formatRole(member.role) }}</span>
                        </div>
                    </div>
                    <div v-if="props.team.length === 0" class="crm-empty-state">
                        No team members attached yet.
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <article class="crm-card">
                <h2 class="crm-panel-title">Admin operating notes</h2>
                <p class="crm-panel-copy">This is the tenant-side equivalent of a command center checklist.</p>

                <div class="mt-5 space-y-3">
                    <div class="crm-list-dot">Keep role assignments intentional so access and reporting stay clean.</div>
                    <div class="crm-list-dot">Treat pending invitations as operational debt, not a harmless backlog.</div>
                    <div class="crm-list-dot">Watch follow-up pressure as closely as lead volume, because pipeline loss starts there.</div>
                </div>
            </article>

            <article class="crm-card">
                <h2 class="crm-panel-title">Readiness snapshot</h2>
                <p class="crm-panel-copy">A quick tenant health strip for people, pipeline, and execution.</p>

                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <div class="crm-card-soft">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <CheckCircle2 class="size-4 text-emerald-500" />
                            Team live
                        </div>
                        <p class="mt-3 text-2xl font-semibold tracking-tight text-foreground">{{ props.stats.teamMembers }}</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">Users already ready to operate.</p>
                    </div>
                    <div class="crm-card-soft">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <Target class="size-4 text-orange-500" />
                            Pipeline
                        </div>
                        <p class="mt-3 text-2xl font-semibold tracking-tight text-foreground">{{ props.stats.totalLeads }}</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">Lead count that the tenant must actively manage.</p>
                    </div>
                    <div class="crm-card-soft">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <ListChecks class="size-4 text-blue-500" />
                            Execution
                        </div>
                        <p class="mt-3 text-2xl font-semibold tracking-tight text-foreground">{{ props.stats.totalTasks }}</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">Tasks keeping that pipeline in motion.</p>
                    </div>
                </div>
            </article>
        </section>
    </div>
</template>
