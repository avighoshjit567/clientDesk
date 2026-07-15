<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CalendarClock, ListChecks, Mail, Users, UsersRound } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import StatCard from '@/components/StatCard.vue';

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

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <PageHeader
            eyebrow="Workspace administration"
            title="Tenant Admin dashboard"
            description="Oversee your workspace team, monitor CRM activity, and manage day-to-day tenant operations."
        />

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <StatCard label="Team members" :value="stats.teamMembers" :icon="Users" />
            <StatCard label="Pending invites" :value="stats.pendingInvitations" :icon="Mail" />
            <StatCard label="Leads" :value="stats.totalLeads" :icon="UsersRound" />
            <StatCard label="Tasks" :value="stats.totalTasks" :icon="ListChecks" />
            <StatCard label="Pending follow-ups" :value="stats.pendingFollowUps" :icon="CalendarClock" />
        </section>

        <section class="rounded-2xl border border-border bg-card p-6">
            <div>
                <h2 class="text-lg font-semibold">Team overview</h2>
                <p class="mt-1 text-sm text-muted-foreground">Everyone with access to this workspace.</p>
            </div>

            <!-- Mobile cards -->
            <div class="mt-5 space-y-3 md:hidden">
                <div v-for="member in team" :key="member.id" class="rounded-xl border border-border p-4">
                    <div class="font-medium">{{ member.name }}</div>
                    <div class="mt-0.5 text-xs text-muted-foreground">{{ member.email }}</div>
                    <div class="mt-2 text-sm capitalize text-muted-foreground">{{ member.role.replaceAll('_', ' ') }}</div>
                </div>
                <div v-if="team.length === 0" class="rounded-xl border border-dashed border-border p-6 text-center text-sm text-muted-foreground">
                    No team members yet. Invite teammates from the Team page.
                </div>
            </div>

            <!-- Desktop table -->
            <div class="mt-5 hidden overflow-x-auto md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-border text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">Name</th>
                            <th class="px-3 py-3 font-medium">Email</th>
                            <th class="px-3 py-3 font-medium">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="member in team"
                            :key="member.id"
                            class="border-b border-border/50 transition hover:bg-muted/50"
                        >
                            <td class="px-3 py-3 font-medium">{{ member.name }}</td>
                            <td class="px-3 py-3 text-muted-foreground">{{ member.email }}</td>
                            <td class="px-3 py-3 capitalize">{{ member.role.replaceAll('_', ' ') }}</td>
                        </tr>
                        <tr v-if="team.length === 0">
                            <td colspan="3" class="px-3 py-10 text-center text-muted-foreground">
                                No team members yet. Invite teammates from the Team page.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
