<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

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
</script>

<template>
    <Head title="Accept invitation" />

    <div class="mx-auto flex min-h-[80vh] max-w-2xl items-center px-4 py-12">
        <div class="w-full rounded-2xl border border-sidebar-border/70 bg-background p-8 dark:border-sidebar-border">
            <p class="text-sm font-medium text-orange-500">Workspace invitation</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight">Join {{ invitation.tenantName || 'workspace' }}</h1>
            <p class="mt-3 text-sm leading-7 text-muted-foreground">
                You are invited as <span class="font-medium text-foreground">{{ invitation.role }}</span> using
                <span class="font-medium text-foreground">{{ invitation.email }}</span>.
            </p>
            <p class="mt-2 text-sm text-muted-foreground">Invitation expiry: {{ invitation.expiresAt || 'No expiry set' }}</p>

            <div class="mt-8">
                <button type="button" :disabled="acceptForm.processing" class="inline-flex items-center rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-orange-400 disabled:cursor-not-allowed disabled:opacity-70" @click="acceptInvitation">
                    {{ acceptForm.processing ? 'Joining...' : 'Accept invitation' }}
                </button>
            </div>
        </div>
    </div>
</template>
