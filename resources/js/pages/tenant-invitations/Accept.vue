<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowRight, ShieldCheck, UserPlus } from 'lucide-vue-next';

defineProps<{
    invitation: {
        tenantName: string | null;
        email: string;
        role: string;
        expiresAt: string | null;
    };
}>();

const acceptForm = useForm({});

const acceptInvitation = () => {
    acceptForm.post(window.location.pathname + '/accept');
};

const formatRole = (value: string) =>
    value
        .split('_')
        .map((segment) => segment.charAt(0).toUpperCase() + segment.slice(1))
        .join(' ');
</script>

<template>
    <Head title="Accept invitation" />

    <div class="mx-auto flex min-h-[82vh] max-w-3xl items-center px-4 py-10">
        <div class="crm-hero w-full">
            <div class="grid gap-6 lg:grid-cols-[1fr_0.85fr]">
                <div>
                    <p class="crm-eyebrow">Workspace invitation</p>
                    <h1 class="crm-title">Join {{ invitation.tenantName || 'workspace' }}</h1>
                    <p class="crm-subtitle">
                        You were invited with <span class="font-medium text-foreground">{{ invitation.email }}</span>
                        as <span class="font-medium text-foreground">{{ formatRole(invitation.role) }}</span>.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="crm-chip crm-chip-success">Invitation ready</span>
                        <span class="crm-chip crm-chip-muted">Expires {{ invitation.expiresAt || 'without expiry' }}</span>
                    </div>

                    <div class="mt-8">
                        <button type="button" :disabled="acceptForm.processing" class="crm-button-primary" @click="acceptInvitation">
                            {{ acceptForm.processing ? 'Joining...' : 'Accept invitation' }}
                            <ArrowRight class="size-4" />
                        </button>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="crm-card-soft">
                        <UserPlus class="size-5 text-orange-500" />
                        <p class="mt-3 text-sm font-semibold text-foreground">Role-aware access</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            You will join the workspace with the permissions assigned to this invitation.
                        </p>
                    </div>
                    <div class="crm-card-soft">
                        <ShieldCheck class="size-5 text-orange-500" />
                        <p class="mt-3 text-sm font-semibold text-foreground">Secure join flow</p>
                        <p class="mt-2 text-sm leading-6 text-muted-foreground">
                            Accept using the same signed-in email so access is tied to the right account.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
