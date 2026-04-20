<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Link2, MailPlus, ShieldCheck, Users } from 'lucide-vue-next';
import { computed } from 'vue';

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

const formatRole = (value: string) =>
    value
        .split('_')
        .map((segment) => segment.charAt(0).toUpperCase() + segment.slice(1))
        .join(' ');

const ownerCount = computed(() => props.team.filter((member) => member.isOwner).length);
</script>

<template>
    <Head title="Team management" />

    <div class="crm-page">
        <section class="crm-hero">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="crm-eyebrow">Team management</p>
                    <h1 class="crm-title">Members and invitations</h1>
                    <p class="crm-subtitle">
                        Keep {{ props.workspace.name || 'your workspace' }} organized with clear roles, controlled invitations, and a cleaner access panel.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="crm-chip crm-chip-success">{{ props.team.length }} active members</span>
                        <span class="crm-chip crm-chip-muted">{{ props.pendingInvitations.length }} pending invites</span>
                        <span class="crm-chip crm-chip-muted">{{ ownerCount }} owner account</span>
                    </div>
                </div>

                <div class="crm-card-soft max-w-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-muted-foreground">Workspace slug</p>
                    <p class="mt-3 text-lg font-semibold tracking-tight text-foreground">
                        {{ props.workspace.slug || 'Not available' }}
                    </p>
                    <p class="mt-2 text-sm leading-6 text-muted-foreground">
                        Roles here drive what the team can actually see and do across ClientDesk.
                    </p>
                </div>
            </div>
        </section>

        <section class="crm-stat-grid xl:grid-cols-3">
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><Users class="size-5" /></div>
                <p class="crm-stat-label">Active members</p>
                <p class="crm-stat-value">{{ props.team.length }}</p>
                <p class="crm-stat-caption">Current teammates who already have access.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><MailPlus class="size-5" /></div>
                <p class="crm-stat-label">Pending invitations</p>
                <p class="crm-stat-value">{{ props.pendingInvitations.length }}</p>
                <p class="crm-stat-caption">Invite links waiting for the right user to accept.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><ShieldCheck class="size-5" /></div>
                <p class="crm-stat-label">Role templates</p>
                <p class="crm-stat-value">{{ props.roleOptions.length }}</p>
                <p class="crm-stat-caption">Current role choices available for workspace assignment.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <form class="crm-card" @submit.prevent="submitInvitation">
                <h2 class="crm-panel-title">Invite teammate</h2>
                <p class="crm-panel-copy">Generate a clean invite link and assign the intended role up front.</p>

                <div class="mt-5 grid gap-4">
                    <div>
                        <label class="crm-label">Email address</label>
                        <input v-model="inviteForm.email" type="email" class="crm-input" />
                        <p v-if="inviteForm.errors.email" class="crm-error">{{ inviteForm.errors.email }}</p>
                    </div>
                    <div>
                        <label class="crm-label">Role</label>
                        <select v-model="inviteForm.role" class="crm-select">
                            <option v-for="role in props.roleOptions" :key="role" :value="role">
                                {{ formatRole(role) }}
                            </option>
                        </select>
                        <p v-if="inviteForm.errors.role" class="crm-error">{{ inviteForm.errors.role }}</p>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="submit" :disabled="inviteForm.processing" class="crm-button-primary">
                        {{ inviteForm.processing ? 'Creating...' : 'Create invitation' }}
                    </button>
                </div>
            </form>

            <article class="crm-card">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="crm-panel-title">Pending invitations</h2>
                        <p class="crm-panel-copy">Share the generated links with teammates after they sign in with the same email.</p>
                    </div>
                    <Link2 class="size-5 text-orange-500" />
                </div>

                <div class="mt-5 space-y-4">
                    <div v-for="invitation in props.pendingInvitations" :key="invitation.id" class="crm-card-soft">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="font-medium text-foreground">{{ invitation.email }}</div>
                                <div class="mt-1 text-sm text-muted-foreground">
                                    {{ formatRole(invitation.role) }} · Expires {{ invitation.expiresAt || 'without expiry' }}
                                </div>
                            </div>
                            <button type="button" class="text-sm font-medium text-red-500" @click="revokeInvitation(invitation.id)">
                                Revoke
                            </button>
                        </div>
                        <div class="mt-3 rounded-xl bg-slate-950/5 px-3 py-2 text-xs break-all text-muted-foreground dark:bg-white/5">
                            {{ invitation.inviteLink }}
                        </div>
                    </div>
                    <div v-if="props.pendingInvitations.length === 0" class="crm-empty-state">
                        No pending invitations yet.
                    </div>
                </div>
            </article>
        </section>

        <section class="crm-card">
            <h2 class="crm-panel-title">Current team</h2>
            <p class="crm-panel-copy">Adjust roles carefully so CRM access stays intentional.</p>

            <div class="crm-table-wrap">
                <table class="crm-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in props.team" :key="member.id">
                            <td>
                                <div class="font-medium text-foreground">{{ member.name }}</div>
                                <div class="mt-1 text-xs text-muted-foreground">
                                    {{ member.isOwner ? 'Owner' : member.isPrimary ? 'Primary member' : 'Member' }}
                                </div>
                            </td>
                            <td>{{ member.email }}</td>
                            <td>
                                <select
                                    :value="member.role"
                                    class="crm-select"
                                    :disabled="member.isOwner"
                                    @change="updateRoleFromEvent(member.id, $event)"
                                >
                                    <option v-for="role in props.roleOptions" :key="role" :value="role">
                                        {{ formatRole(role) }}
                                    </option>
                                </select>
                            </td>
                            <td>{{ member.joinedAt || 'Recently added' }}</td>
                            <td class="text-right">
                                <button
                                    v-if="!member.isOwner"
                                    type="button"
                                    class="text-sm font-medium text-red-500"
                                    @click="removeMember(member.id)"
                                >
                                    Remove
                                </button>
                            </td>
                        </tr>
                        <tr v-if="props.team.length === 0">
                            <td colspan="5">
                                <div class="crm-empty-state">No team members added yet.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
