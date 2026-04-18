<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tenant Admin',
                href: '/tenant-admin/dashboard',
            },
            {
                title: 'Team',
                href: '/tenant-admin/team',
            },
        ],
    },
});

const props = defineProps<{
    workspace: {
        name: string | null;
        slug: string | null;
    };
    roleOptions: string[];
    team: Array<{
        id: number;
        name: string;
        email: string;
        role: string;
        isPrimary: boolean;
        joinedAt: string | null;
        isOwner: boolean;
    }>;
    pendingInvitations: Array<{
        id: number;
        email: string;
        role: string;
        expiresAt: string | null;
        inviteLink: string;
    }>;
}>();

const inviteForm = useForm({
    email: '',
    role: props.roleOptions[2] ?? props.roleOptions[0] ?? 'sales_rep',
});

const submitInvitation = () => {
    inviteForm.post('/tenant-admin/invitations', {
        preserveScroll: true,
        onSuccess: () => inviteForm.reset('email'),
    });
};

const updateRole = (memberId: number, role: string) => {
    router.patch(`/tenant-admin/team-members/${memberId}`, { role }, { preserveScroll: true });
};

const updateRoleFromEvent = (memberId: number, event: Event) => {
    updateRole(memberId, (event.target as HTMLSelectElement).value);
};

const removeMember = (memberId: number) => {
    router.delete(`/tenant-admin/team-members/${memberId}`, { preserveScroll: true });
};

const revokeInvitation = (invitationId: number) => {
    router.delete(`/tenant-admin/invitations/${invitationId}`, { preserveScroll: true });
};
</script>

<template>
    <Head title="Team management" />

    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
        <section class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <p class="text-sm font-medium text-orange-500">Workspace team management</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Team members and invitations</h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 text-muted-foreground">
                Invite teammates into {{ workspace.name || 'your workspace' }}, assign roles, and keep workspace access organized.
            </p>
        </section>

        <section class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <form class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border" @submit.prevent="submitInvitation">
                <h2 class="text-lg font-semibold">Invite teammate</h2>
                <p class="mt-1 text-sm text-muted-foreground">Create a reusable invite link for a new workspace member.</p>

                <div class="mt-5 grid gap-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium">Email address</label>
                        <input v-model="inviteForm.email" type="email" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500" />
                        <p v-if="inviteForm.errors.email" class="mt-2 text-sm text-red-500">{{ inviteForm.errors.email }}</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Role</label>
                        <select v-model="inviteForm.role" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                            <option v-for="role in roleOptions" :key="role" :value="role">{{ role }}</option>
                        </select>
                        <p v-if="inviteForm.errors.role" class="mt-2 text-sm text-red-500">{{ inviteForm.errors.role }}</p>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="submit" :disabled="inviteForm.processing" class="inline-flex items-center rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-orange-400 disabled:cursor-not-allowed disabled:opacity-70">
                        {{ inviteForm.processing ? 'Creating...' : 'Create invitation' }}
                    </button>
                </div>
            </form>

            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">Pending invitations</h2>
                        <p class="mt-1 text-sm text-muted-foreground">Share these links with the invited teammate after they sign in with the same email.</p>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    <div v-for="invitation in pendingInvitations" :key="invitation.id" class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="font-medium">{{ invitation.email }}</div>
                                <div class="mt-1 text-sm text-muted-foreground">Role: {{ invitation.role }} · Expires: {{ invitation.expiresAt || 'No expiry' }}</div>
                            </div>
                            <button type="button" class="text-sm font-medium text-red-500" @click="revokeInvitation(invitation.id)">Revoke</button>
                        </div>
                        <div class="mt-3 rounded-lg bg-muted px-3 py-2 text-xs break-all text-muted-foreground">{{ invitation.inviteLink }}</div>
                    </div>
                    <div v-if="pendingInvitations.length === 0" class="rounded-xl border border-dashed border-sidebar-border/70 p-6 text-sm text-muted-foreground dark:border-sidebar-border">
                        No pending invitations yet.
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <h2 class="text-lg font-semibold">Current team</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border">
                        <tr>
                            <th class="px-3 py-3 font-medium">Name</th>
                            <th class="px-3 py-3 font-medium">Email</th>
                            <th class="px-3 py-3 font-medium">Role</th>
                            <th class="px-3 py-3 font-medium">Joined</th>
                            <th class="px-3 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in team" :key="member.id" class="border-b border-sidebar-border/40 align-top dark:border-sidebar-border/60">
                            <td class="px-3 py-3">
                                <div class="font-medium">{{ member.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ member.isOwner ? 'Owner' : member.isPrimary ? 'Primary member' : 'Member' }}</div>
                            </td>
                            <td class="px-3 py-3">{{ member.email }}</td>
                            <td class="px-3 py-3">
                                <select
                                    :value="member.role"
                                    class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500"
                                    :disabled="member.isOwner"
                                    @change="updateRoleFromEvent(member.id, $event)"
                                >
                                    <option v-for="role in roleOptions" :key="role" :value="role">{{ role }}</option>
                                </select>
                            </td>
                            <td class="px-3 py-3 text-muted-foreground">{{ member.joinedAt || 'Recently added' }}</td>
                            <td class="px-3 py-3 text-right">
                                <button v-if="!member.isOwner" type="button" class="text-sm font-medium text-red-500" @click="removeMember(member.id)">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
