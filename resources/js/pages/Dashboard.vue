<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

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

defineProps<{
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
    };
}>();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
        <section class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-500">ClientDesk workspace</p>
                    <h1 class="mt-1 text-2xl font-semibold tracking-tight">{{ workspace.name }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-muted-foreground">
                        Your initial workspace foundation is ready. Next steps are team onboarding, role management,
                        leads, and sales operations.
                    </p>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 px-4 py-3 text-sm dark:border-sidebar-border">
                    <p><span class="font-medium">Plan:</span> {{ workspace.plan ?? 'Not assigned' }}</p>
                    <p class="mt-1"><span class="font-medium">Timezone:</span> {{ workspace.timezone }}</p>
                    <p class="mt-1"><span class="font-medium">Slug:</span> {{ workspace.slug }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Team members</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.memberCount }}</p>
                <p class="mt-2 text-sm text-muted-foreground">Current users attached to this tenant workspace.</p>
            </div>

            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Pending invitations</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.pendingInvitationCount }}</p>
                <p class="mt-2 text-sm text-muted-foreground">Outstanding invites waiting for team members.</p>
            </div>

            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Default dialing prefix</p>
                <p class="mt-2 text-3xl font-semibold">{{ workspace.phoneCountryCode ?? 'N/A' }}</p>
                <p class="mt-2 text-sm text-muted-foreground">Useful for telecalling and contact workflows.</p>
            </div>
        </section>

        <section class="rounded-2xl border border-dashed border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <h2 class="text-lg font-semibold">What comes next</h2>
            <ul class="mt-4 space-y-3 text-sm leading-7 text-muted-foreground">
                <li>Set up tenant onboarding completion flow and workspace switching.</li>
                <li>Add role and permission layers for tenant admins, managers, and sales reps.</li>
                <li>Build CRM modules for leads, follow-ups, tasks, and call activity.</li>
            </ul>
        </section>
    </div>
</template>
