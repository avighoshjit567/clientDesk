<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { MailCheck } from 'lucide-vue-next';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Verify email',
        description: 'Please verify your email address by clicking the link we just sent to your inbox.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Email verification" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-700 dark:text-emerald-300"
    >
        A new verification link has been sent to the email address you provided during registration.
    </div>

    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-border/60 bg-muted/50 p-4">
        <div class="flex size-10 items-center justify-center rounded-2xl bg-orange-500/10 text-orange-500">
            <MailCheck class="size-5" />
        </div>
        <div>
            <p class="text-sm font-medium text-foreground">One last security step</p>
            <p class="mt-1 text-sm leading-6 text-muted-foreground">
                Verify your inbox before continuing into your workspace and invitation flows.
            </p>
        </div>
    </div>

    <Form
        v-bind="send.form()"
        class="space-y-4 text-center"
        v-slot="{ processing }"
    >
        <Button :disabled="processing" class="crm-button-primary h-12 w-full">
            <Spinner v-if="processing" />
            Resend verification email
        </Button>

        <TextLink :href="logout()" as="button" class="mx-auto block text-sm">
            Log out
        </TextLink>
    </Form>
</template>
