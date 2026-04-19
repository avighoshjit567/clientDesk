<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { MailCheck } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Forgot password',
        description: 'Enter your email and we will send you a secure password reset link.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Forgot password" />

    <div
        v-if="status"
        class="mb-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-700 dark:text-emerald-300"
    >
        {{ status }}
    </div>

    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-border/60 bg-muted/50 p-4">
        <div class="flex size-10 items-center justify-center rounded-2xl bg-orange-500/10 text-orange-500">
            <MailCheck class="size-5" />
        </div>
        <div>
            <p class="text-sm font-medium text-foreground">Reset access securely</p>
            <p class="mt-1 text-sm leading-6 text-muted-foreground">
                We will email a reset link so you can get back into your workspace safely.
            </p>
        </div>
    </div>

    <div class="space-y-6">
        <Form v-bind="email.form()" v-slot="{ errors, processing }" class="space-y-5">
            <div class="grid gap-2">
                <Label for="email" class="text-sm font-medium">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="off"
                    autofocus
                    placeholder="email@example.com"
                    class="crm-input h-12"
                />
                <InputError :message="errors.email" />
            </div>

            <Button
                class="crm-button-primary h-12 w-full"
                :disabled="processing"
                data-test="email-password-reset-link-button"
            >
                <Spinner v-if="processing" />
                Email password reset link
            </Button>
        </Form>

        <div class="text-center text-sm text-muted-foreground">
            Or, return to
            <TextLink :href="login()">log in</TextLink>
        </div>
    </div>
</template>
