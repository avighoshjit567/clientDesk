<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    BriefcaseBusiness,
    PhoneCall,
    Target,
    UserPlus,
    Users,
} from 'lucide-vue-next';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    },
});

const props = defineProps<{
    workspace: {
        name: string;
        slug: string;
        status: string;
        timezone: string;
        plan: string | null;
        currencyCode: string | null;
        phoneCountryCode: string | null;
    };
    stats: {
        memberCount: number;
        pendingInvitationCount: number;
        leadCount: number;
        taskCount: number;
    };
}>();
</script>

<template>
    <Head title="Dashboard" />

    <div class="crm-page">
        <section class="crm-hero">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="crm-eyebrow">Workspace overview</p>
                    <h1 class="crm-title">{{ props.workspace.name }}</h1>
                    <p class="crm-subtitle">
                        A cleaner operating panel for your team. Track pipeline activity, manage access,
                        and keep sales execution focused from one place.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="crm-chip crm-chip-success">{{ props.workspace.status }}</span>
                        <span class="crm-chip crm-chip-muted">{{ props.workspace.plan ?? 'No plan assigned' }}</span>
                        <span class="crm-chip crm-chip-muted">{{ props.workspace.timezone }}</span>
                        <span class="crm-chip crm-chip-muted">{{ props.workspace.slug }}</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:w-[360px]">
                    <Link href="/tenant-admin/team" class="crm-button-primary">
                        Manage team
                        <ArrowRight class="size-4" />
                    </Link>
                    <Link href="/leads" class="crm-button-secondary">
                        Open leads
                    </Link>
                    <div class="crm-card-soft sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-muted-foreground">
                            Workspace details
                        </p>
                        <div class="mt-3 grid gap-3 text-sm text-muted-foreground sm:grid-cols-2">
                            <div>
                                <p class="font-medium text-foreground">Currency</p>
                                <p class="mt-1">{{ props.workspace.currencyCode ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-foreground">Dialing code</p>
                                <p class="mt-1">{{ props.workspace.phoneCountryCode ?? 'Not set' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="crm-stat-grid">
            <article class="crm-stat-card">
                <div class="crm-stat-icon">
                    <Users class="size-5" />
                </div>
                <p class="crm-stat-label">Team members</p>
                <p class="crm-stat-value">{{ props.stats.memberCount }}</p>
                <p class="crm-stat-caption">Everyone currently attached to this tenant workspace.</p>
            </article>

            <article class="crm-stat-card">
                <div class="crm-stat-icon">
                    <UserPlus class="size-5" />
                </div>
                <p class="crm-stat-label">Pending invitations</p>
                <p class="crm-stat-value">{{ props.stats.pendingInvitationCount }}</p>
                <p class="crm-stat-caption">Team invites waiting to be accepted and activated.</p>
            </article>

            <article class="crm-stat-card">
                <div class="crm-stat-icon">
                    <Target class="size-5" />
                </div>
                <p class="crm-stat-label">Active leads</p>
                <p class="crm-stat-value">{{ props.stats.leadCount }}</p>
                <p class="crm-stat-caption">Lead records already flowing through your workspace.</p>
            </article>

            <article class="crm-stat-card">
                <div class="crm-stat-icon">
                    <BriefcaseBusiness class="size-5" />
                </div>
                <p class="crm-stat-label">Open tasks</p>
                <p class="crm-stat-value">{{ props.stats.taskCount }}</p>
                <p class="crm-stat-caption">Assigned execution items across the current team.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <article class="crm-card">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="crm-panel-title">Operational priorities</h2>
                        <p class="crm-panel-copy">Where this workspace should focus next.</p>
                    </div>
                    <PhoneCall class="size-5 text-orange-500" />
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="crm-card-soft">
                        <p class="text-sm font-semibold text-foreground">Team readiness</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Keep the org chart clear, finalize invitations, and make sure the right people are attached to the workspace.
                        </p>
                    </div>
                    <div class="crm-card-soft">
                        <p class="text-sm font-semibold text-foreground">Lead hygiene</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Standardize sources, assign ownership quickly, and keep every lead traceable from day one.
                        </p>
                    </div>
                    <div class="crm-card-soft">
                        <p class="text-sm font-semibold text-foreground">Execution rhythm</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Build a habit of task ownership and follow-up discipline so the pipeline stays alive.
                        </p>
                    </div>
                    <div class="crm-card-soft">
                        <p class="text-sm font-semibold text-foreground">Data quality</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Capture phone, source, assignment, and notes consistently so later reporting stays useful.
                        </p>
                    </div>
                </div>
            </article>

            <article class="crm-card">
                <h2 class="crm-panel-title">Suggested next moves</h2>
                <p class="crm-panel-copy">A practical sequence for getting this workspace production ready.</p>

                <div class="mt-5 space-y-3">
                    <div class="crm-list-dot">Invite the first working team members and set their roles clearly.</div>
                    <div class="crm-list-dot">Create your first real leads with proper source tags and ownership.</div>
                    <div class="crm-list-dot">Set follow-up and task discipline before the pipeline starts growing.</div>
                </div>
            </article>
        </section>
    </div>
</template>
