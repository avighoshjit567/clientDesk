<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CalendarCheck, Mail, Phone, Users, UsersRound } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import StatCard from '@/components/StatCard.vue';
import StatusBadge from '@/components/StatusBadge.vue';

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
        leadCount: number;
        taskCount: number;
    };
}>();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <PageHeader
            eyebrow="Workspace"
            :title="workspace.name"
            description="A quick overview of your team, leads, and open work in this workspace."
        >
            <template #aside>
                <div class="flex shrink-0 flex-col gap-2 rounded-xl border border-border px-4 py-3 text-sm">
                    <div class="flex items-center justify-between gap-6">
                        <span class="text-muted-foreground">Plan</span>
                        <span class="font-medium">{{ workspace.plan ?? 'Not assigned' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-6">
                        <span class="text-muted-foreground">Timezone</span>
                        <span class="font-medium">{{ workspace.timezone }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-6">
                        <span class="text-muted-foreground">Status</span>
                        <StatusBadge :status="workspace.status" />
                    </div>
                </div>
            </template>
        </PageHeader>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatCard
                label="Team members"
                :value="stats.memberCount"
                hint="Users attached to this workspace."
                :icon="Users"
            />
            <StatCard
                label="Pending invitations"
                :value="stats.pendingInvitationCount"
                hint="Invites waiting to be accepted."
                :icon="Mail"
            />
            <StatCard
                label="Leads"
                :value="stats.leadCount"
                hint="Leads tracked in this workspace."
                :icon="UsersRound"
            />
            <StatCard
                label="Tasks"
                :value="stats.taskCount"
                hint="Open work items for your team."
                :icon="CalendarCheck"
            />
        </section>

        <section class="rounded-2xl border border-border bg-card p-6">
            <h2 class="text-lg font-semibold">Workspace details</h2>
            <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-border p-4">
                    <dt class="text-muted-foreground">Workspace slug</dt>
                    <dd class="mt-1 font-medium">{{ workspace.slug }}</dd>
                </div>
                <div class="rounded-xl border border-border p-4">
                    <dt class="text-muted-foreground">Currency</dt>
                    <dd class="mt-1 font-medium">{{ workspace.currencyCode ?? 'Not set' }}</dd>
                </div>
                <div class="rounded-xl border border-border p-4">
                    <dt class="flex items-center gap-1.5 text-muted-foreground"><Phone class="h-3.5 w-3.5" /> Dialing prefix</dt>
                    <dd class="mt-1 font-medium">{{ workspace.phoneCountryCode ?? 'Not set' }}</dd>
                </div>
                <div class="rounded-xl border border-border p-4">
                    <dt class="text-muted-foreground">Timezone</dt>
                    <dd class="mt-1 font-medium">{{ workspace.timezone }}</dd>
                </div>
            </dl>
        </section>
    </div>
</template>
