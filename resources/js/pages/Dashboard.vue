<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Activity,
    ArrowRight,
    BriefcaseBusiness,
    CheckCircle2,
    Clock3,
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

const focusRows = [
    {
        title: 'Invite and activate the team',
        value: props.stats.pendingInvitationCount,
        description: 'Pending invites are the first friction point in most CRM rollouts.',
        icon: UserPlus,
        href: '/tenant-admin/team',
    },
    {
        title: 'Own incoming opportunities',
        value: props.stats.leadCount,
        description: 'Keep fresh leads visible, assigned, and ready for follow-up.',
        icon: Target,
        href: '/leads',
    },
    {
        title: 'Push execution forward',
        value: props.stats.taskCount,
        description: 'Tasks are where pipeline discipline becomes real execution.',
        icon: BriefcaseBusiness,
        href: '/tasks',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <div class="crm-page">
        <section class="crm-hero">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-3xl">
                    <p class="crm-eyebrow">Sales cockpit</p>
                    <h1 class="crm-title">{{ props.workspace.name }}</h1>
                    <p class="crm-subtitle">
                        Reworked toward a more real CRM structure: a focused command area, activity-first priorities, and clearer paths into leads, tasks, and team operations.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="crm-chip crm-chip-success">{{ props.workspace.status }}</span>
                        <span class="crm-chip crm-chip-muted">{{ props.workspace.plan ?? 'No plan assigned' }}</span>
                        <span class="crm-chip crm-chip-muted">{{ props.workspace.timezone }}</span>
                        <span class="crm-chip crm-chip-muted">{{ props.workspace.slug }}</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:w-[380px]">
                    <Link href="/leads" class="crm-button-primary h-12">
                        Open leads
                        <ArrowRight class="size-4" />
                    </Link>
                    <Link href="/tasks" class="crm-button-secondary h-12">Open tasks</Link>
                    <div class="crm-card-soft sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-muted-foreground">Workspace control</p>
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
                <p class="crm-stat-label">Team coverage</p>
                <p class="crm-stat-value">{{ props.stats.memberCount }}</p>
                <p class="crm-stat-caption">People currently inside the workspace and able to act.</p>
            </article>

            <article class="crm-stat-card">
                <div class="crm-stat-icon">
                    <UserPlus class="size-5" />
                </div>
                <p class="crm-stat-label">Activation queue</p>
                <p class="crm-stat-value">{{ props.stats.pendingInvitationCount }}</p>
                <p class="crm-stat-caption">Pending invitations still blocking full team readiness.</p>
            </article>

            <article class="crm-stat-card">
                <div class="crm-stat-icon">
                    <Target class="size-5" />
                </div>
                <p class="crm-stat-label">Open opportunities</p>
                <p class="crm-stat-value">{{ props.stats.leadCount }}</p>
                <p class="crm-stat-caption">Lead volume that needs ownership and fast response.</p>
            </article>

            <article class="crm-stat-card">
                <div class="crm-stat-icon">
                    <Activity class="size-5" />
                </div>
                <p class="crm-stat-label">Execution load</p>
                <p class="crm-stat-value">{{ props.stats.taskCount }}</p>
                <p class="crm-stat-caption">Current task pressure across the sales workflow.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <article class="crm-card">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="crm-panel-title">Today’s focus queue</h2>
                        <p class="crm-panel-copy">Borrowing from stronger CRM patterns, this puts the next actions ahead of decorative panels.</p>
                    </div>
                    <Clock3 class="size-5 text-orange-500" />
                </div>

                <div class="mt-5 space-y-4">
                    <Link
                        v-for="row in focusRows"
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
                <h2 class="crm-panel-title">Workspace health</h2>
                <p class="crm-panel-copy">A small operator rail, similar to what good CRM command centers keep in view.</p>

                <div class="mt-5 space-y-3">
                    <div class="crm-card-soft">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium text-foreground">Workspace status</p>
                            <span class="crm-chip crm-chip-success">{{ props.workspace.status }}</span>
                        </div>
                        <p class="mt-2 text-sm text-muted-foreground">The workspace is live and ready for real usage.</p>
                    </div>
                    <div class="crm-card-soft">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium text-foreground">Plan</p>
                            <span class="crm-chip crm-chip-muted">{{ props.workspace.plan ?? 'None' }}</span>
                        </div>
                        <p class="mt-2 text-sm text-muted-foreground">Keep pricing and limits visible as the product matures.</p>
                    </div>
                    <div class="crm-card-soft">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium text-foreground">Timezone</p>
                            <span class="crm-chip crm-chip-muted">{{ props.workspace.timezone }}</span>
                        </div>
                        <p class="mt-2 text-sm text-muted-foreground">Important for future reminders, call schedules, and follow-up timing.</p>
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <article class="crm-card">
                <h2 class="crm-panel-title">Pipeline operating notes</h2>
                <p class="crm-panel-copy">A more CRM-native framing of what this team should do next.</p>

                <div class="mt-5 space-y-3">
                    <div class="crm-list-dot">Finish workspace activation first, because unused invites slow everything else down.</div>
                    <div class="crm-list-dot">Keep leads assigned early so the team knows exactly who owns the next conversation.</div>
                    <div class="crm-list-dot">Use tasks as the daily execution layer, not just as a passive list.</div>
                </div>
            </article>

            <article class="crm-card">
                <h2 class="crm-panel-title">Execution snapshot</h2>
                <p class="crm-panel-copy">A compact board, inspired by activity-first CRMs rather than starter dashboards.</p>

                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <div class="crm-card-soft">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <CheckCircle2 class="size-4 text-emerald-500" />
                            Team ready
                        </div>
                        <p class="mt-3 text-2xl font-semibold tracking-tight text-foreground">{{ props.stats.memberCount }}</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">Active members available to work the pipeline.</p>
                    </div>
                    <div class="crm-card-soft">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <Target class="size-4 text-orange-500" />
                            Lead queue
                        </div>
                        <p class="mt-3 text-2xl font-semibold tracking-tight text-foreground">{{ props.stats.leadCount }}</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">Open opportunities waiting for structure and follow-up.</p>
                    </div>
                    <div class="crm-card-soft">
                        <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                            <BriefcaseBusiness class="size-4 text-blue-500" />
                            Work in motion
                        </div>
                        <p class="mt-3 text-2xl font-semibold tracking-tight text-foreground">{{ props.stats.taskCount }}</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">Action items currently driving the pipeline forward.</p>
                    </div>
                </div>
            </article>
        </section>
    </div>
</template>
