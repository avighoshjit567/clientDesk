<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

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

defineProps<{
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
</script>

<template>
    <Head title="Tenant Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
        <section class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <p class="text-sm font-medium text-orange-500">Workspace administration</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Tenant Admin dashboard</h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 text-muted-foreground">
                Oversee your workspace team, monitor CRM activity, and manage day-to-day tenant operations.
            </p>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Team members</p><p class="mt-2 text-3xl font-semibold">{{ stats.teamMembers }}</p></div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Pending invites</p><p class="mt-2 text-3xl font-semibold">{{ stats.pendingInvitations }}</p></div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Leads</p><p class="mt-2 text-3xl font-semibold">{{ stats.totalLeads }}</p></div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Tasks</p><p class="mt-2 text-3xl font-semibold">{{ stats.totalTasks }}</p></div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"><p class="text-sm text-muted-foreground">Pending follow-ups</p><p class="mt-2 text-3xl font-semibold">{{ stats.pendingFollowUps }}</p></div>
        </section>

        <section class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <h2 class="text-lg font-semibold">Team overview</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border">
                        <tr>
                            <th class="px-3 py-3 font-medium">Name</th>
                            <th class="px-3 py-3 font-medium">Email</th>
                            <th class="px-3 py-3 font-medium">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in team" :key="member.id" class="border-b border-sidebar-border/40 dark:border-sidebar-border/60">
                            <td class="px-3 py-3 font-medium">{{ member.name }}</td>
                            <td class="px-3 py-3">{{ member.email }}</td>
                            <td class="px-3 py-3">{{ member.role }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
