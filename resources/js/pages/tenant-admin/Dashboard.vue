<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    CalendarClock,
    ListChecks,
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
</script>

<template>
    <Head title="Tenant Admin" />

    <div class="crm-page">
        <section class="crm-hero">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="crm-eyebrow">Workspace administration</p>
                    <h1 class="crm-title">Tenant Admin dashboard</h1>
                    <p class="crm-subtitle">
                        Keep the workspace team organized, maintain pipeline discipline, and make sure activity stays visible.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="crm-chip crm-chip-success">{{ props.stats.teamMembers }} team members</span>
                        <span class="crm-chip crm-chip-muted">{{ props.stats.pendingInvitations }} pending invites</span>
                        <span class="crm-chip crm-chip-muted">{{ props.stats.pendingFollowUps }} follow-ups due</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:w-[360px]">
                    <Link href="/tenant-admin/team" class="crm-button-primary">
                        Open team panel
                        <ArrowRight class="size-4" />
                    </Link>
                    <Link href="/tasks" class="crm-button-secondary">View tasks</Link>
                    <div class="crm-card-soft sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-muted-foreground">Admin focus</p>
                        <p class="mt-3 text-sm leading-6 text-muted-foreground">
                            Improve team clarity, reduce invite lag, and keep task ownership obvious at a glance.
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
                <p class="crm-stat-caption">Users already operating inside this workspace.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><UserPlus class="size-5" /></div>
                <p class="crm-stat-label">Pending invites</p>
                <p class="crm-stat-value">{{ props.stats.pendingInvitations }}</p>
                <p class="crm-stat-caption">Invitations still waiting for the right teammate.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><Target class="size-5" /></div>
                <p class="crm-stat-label">Leads</p>
                <p class="crm-stat-value">{{ props.stats.totalLeads }}</p>
                <p class="crm-stat-caption">Leads already captured for your sales team.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><ListChecks class="size-5" /></div>
                <p class="crm-stat-label">Tasks</p>
                <p class="crm-stat-value">{{ props.stats.totalTasks }}</p>
                <p class="crm-stat-caption">Current workload being executed by the team.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><CalendarClock class="size-5" /></div>
                <p class="crm-stat-label">Pending follow-ups</p>
                <p class="crm-stat-value">{{ props.stats.pendingFollowUps }}</p>
                <p class="crm-stat-caption">Scheduled touchpoints that still need action.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <article class="crm-card">
                <h2 class="crm-panel-title">Team overview</h2>
                <p class="crm-panel-copy">The people currently attached to this workspace.</p>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div v-for="member in props.team" :key="member.id" class="crm-card-soft">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-foreground">{{ member.name }}</p>
                                <p class="mt-1 text-sm text-muted-foreground">{{ member.email }}</p>
                            </div>
                            <span class="crm-chip crm-chip-muted">{{ formatRole(member.role) }}</span>
                        </div>
                    </div>
                    <div v-if="props.team.length === 0" class="crm-empty-state sm:col-span-2">
                        No team members are attached yet.
                    </div>
                </div>
            </article>

            <article class="crm-card">
                <h2 class="crm-panel-title">Admin checklist</h2>
                <p class="crm-panel-copy">A simple routine that keeps the workspace clean and professional.</p>

                <div class="mt-5 space-y-3">
                    <div class="crm-list-dot">Invite only the roles you actually need for current operations.</div>
                    <div class="crm-list-dot">Review ownership when leads start coming in fast.</div>
                    <div class="crm-list-dot">Use tasks and follow-ups together, so nothing gets lost after first contact.</div>
                </div>
            </article>
        </section>
    </div>
</template>
