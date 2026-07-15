<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/PageHeader.vue';
import SelectInput from '@/components/SelectInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

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
        onSuccess: () => {
            inviteForm.reset('email');
            toast.success('Invitation created');
        },
    });
};

const updateRole = (memberId: number, role: string) => {
    router.patch(
        `/tenant-admin/team-members/${memberId}`,
        { role },
        {
            preserveScroll: true,
            onSuccess: () => toast.success('Role updated'),
        },
    );
};

const updateRoleFromEvent = (memberId: number, event: Event) => {
    updateRole(memberId, (event.target as HTMLSelectElement).value);
};

const removeMember = (memberId: number) => {
    router.delete(`/tenant-admin/team-members/${memberId}`, {
        preserveScroll: true,
        onSuccess: () => toast.success('Member removed'),
    });
};

const revokeInvitation = (invitationId: number) => {
    router.delete(`/tenant-admin/invitations/${invitationId}`, {
        preserveScroll: true,
        onSuccess: () => toast.success('Invitation revoked'),
    });
};

const copyInviteLink = async (link: string) => {
    await navigator.clipboard.writeText(link);
    toast.success('Invite link copied');
};
</script>

<template>
    <Head title="Team management" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <PageHeader
            eyebrow="Workspace administration"
            title="Team members & invitations"
            :description="`Invite teammates into ${workspace.name || 'your workspace'}, assign roles, and keep workspace access organized.`"
        />

        <section class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <form class="rounded-2xl border border-border bg-card p-6" @submit.prevent="submitInvitation">
                <h2 class="text-lg font-semibold">Invite teammate</h2>
                <p class="mt-1 text-sm text-muted-foreground">Create a reusable invite link for a new workspace member.</p>

                <div class="mt-5 grid gap-4">
                    <div class="grid gap-2">
                        <Label for="invite-email">Email address</Label>
                        <Input
                            id="invite-email"
                            v-model="inviteForm.email"
                            type="email"
                            required
                            placeholder="teammate@company.com"
                            :aria-invalid="Boolean(inviteForm.errors.email)"
                        />
                        <InputError :message="inviteForm.errors.email" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="invite-role">Role</Label>
                        <SelectInput id="invite-role" v-model="inviteForm.role" class="capitalize">
                            <option v-for="role in roleOptions" :key="role" :value="role">{{ role.replaceAll('_', ' ') }}</option>
                        </SelectInput>
                        <InputError :message="inviteForm.errors.role" />
                    </div>
                </div>

                <div class="mt-5">
                    <Button type="submit" :disabled="inviteForm.processing">
                        <Spinner v-if="inviteForm.processing" />
                        Create invitation
                    </Button>
                </div>
            </form>

            <div class="rounded-2xl border border-border bg-card p-6">
                <h2 class="text-lg font-semibold">Pending invitations</h2>
                <p class="mt-1 text-sm text-muted-foreground">
                    Share these links with the invited teammate after they sign in with the same email.
                </p>

                <div class="mt-5 space-y-4">
                    <div v-for="invitation in pendingInvitations" :key="invitation.id" class="rounded-xl border border-border p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="font-medium">{{ invitation.email }}</div>
                                <div class="mt-1 text-sm text-muted-foreground">
                                    <span class="capitalize">{{ invitation.role.replaceAll('_', ' ') }}</span>
                                    · Expires: {{ invitation.expiresAt || 'No expiry' }}
                                </div>
                            </div>
                            <ConfirmDialog
                                title="Revoke invitation?"
                                :description="`The invite link for ${invitation.email} will stop working immediately.`"
                                confirm-label="Revoke"
                                @confirm="revokeInvitation(invitation.id)"
                            >
                                <Button variant="ghost" size="sm" class="text-destructive hover:text-destructive">Revoke</Button>
                            </ConfirmDialog>
                        </div>
                        <button
                            type="button"
                            class="mt-3 w-full rounded-lg bg-muted px-3 py-2 text-left text-xs break-all text-muted-foreground transition hover:bg-muted/70"
                            title="Click to copy"
                            @click="copyInviteLink(invitation.inviteLink)"
                        >
                            {{ invitation.inviteLink }}
                        </button>
                    </div>
                    <div v-if="pendingInvitations.length === 0" class="rounded-xl border border-dashed border-border p-6 text-sm text-muted-foreground">
                        No pending invitations. Invite a teammate using the form.
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-border bg-card p-6">
            <h2 class="text-lg font-semibold">Current team</h2>
            <p class="mt-1 text-sm text-muted-foreground">Manage roles and access for everyone in this workspace.</p>

            <!-- Mobile cards -->
            <div class="mt-5 space-y-3 md:hidden">
                <div v-for="member in team" :key="member.id" class="rounded-xl border border-border p-4">
                    <div class="font-medium">{{ member.name }}</div>
                    <div class="mt-0.5 text-xs text-muted-foreground">
                        {{ member.isOwner ? 'Owner' : member.isPrimary ? 'Primary member' : 'Member' }} · {{ member.email }}
                    </div>
                    <div class="mt-3 grid gap-2">
                        <SelectInput
                            :model-value="member.role"
                            class="capitalize"
                            :disabled="member.isOwner"
                            :aria-label="`Role for ${member.name}`"
                            @change="updateRoleFromEvent(member.id, $event)"
                        >
                            <option v-for="role in roleOptions" :key="role" :value="role">{{ role.replaceAll('_', ' ') }}</option>
                        </SelectInput>
                        <div class="flex items-center justify-between text-sm text-muted-foreground">
                            <span>Joined: {{ member.joinedAt || 'Recently added' }}</span>
                            <ConfirmDialog
                                v-if="!member.isOwner"
                                title="Remove team member?"
                                :description="`${member.name} will lose access to this workspace. This cannot be undone.`"
                                confirm-label="Remove"
                                @confirm="removeMember(member.id)"
                            >
                                <Button variant="ghost" size="sm" class="text-destructive hover:text-destructive">Remove</Button>
                            </ConfirmDialog>
                        </div>
                    </div>
                </div>
                <div v-if="team.length === 0" class="rounded-xl border border-dashed border-border p-6 text-center text-sm text-muted-foreground">
                    No team members yet.
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
                            <th class="px-3 py-3 font-medium">Joined</th>
                            <th class="px-3 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in team" :key="member.id" class="border-b border-border/50 align-middle transition hover:bg-muted/50">
                            <td class="px-3 py-3">
                                <div class="font-medium">{{ member.name }}</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ member.isOwner ? 'Owner' : member.isPrimary ? 'Primary member' : 'Member' }}
                                </div>
                            </td>
                            <td class="px-3 py-3 text-muted-foreground">{{ member.email }}</td>
                            <td class="px-3 py-3">
                                <SelectInput
                                    :model-value="member.role"
                                    class="w-40 capitalize"
                                    :disabled="member.isOwner"
                                    :aria-label="`Role for ${member.name}`"
                                    @change="updateRoleFromEvent(member.id, $event)"
                                >
                                    <option v-for="role in roleOptions" :key="role" :value="role">{{ role.replaceAll('_', ' ') }}</option>
                                </SelectInput>
                            </td>
                            <td class="px-3 py-3 text-muted-foreground">{{ member.joinedAt || 'Recently added' }}</td>
                            <td class="px-3 py-3 text-right">
                                <ConfirmDialog
                                    v-if="!member.isOwner"
                                    title="Remove team member?"
                                    :description="`${member.name} will lose access to this workspace. This cannot be undone.`"
                                    confirm-label="Remove"
                                    @confirm="removeMember(member.id)"
                                >
                                    <Button variant="ghost" size="sm" class="text-destructive hover:text-destructive">Remove</Button>
                                </ConfirmDialog>
                            </td>
                        </tr>
                        <tr v-if="team.length === 0">
                            <td colspan="5" class="px-3 py-10 text-center text-muted-foreground">No team members yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
