<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { LockKeyhole } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';

defineOptions({
    layout: {
        title: 'Confirm your password',
        description: 'This is a secure area. Confirm your password before continuing.',
    },
});
</script>

<template>
    <Head title="Confirm password" />

    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-border/60 bg-muted/50 p-4">
        <div class="flex size-10 items-center justify-center rounded-2xl bg-orange-500/10 text-orange-500">
            <LockKeyhole class="size-5" />
        </div>
        <div>
            <p class="text-sm font-medium text-foreground">Protected action</p>
            <p class="mt-1 text-sm leading-6 text-muted-foreground">
                Re-enter your password to continue with this protected flow.
            </p>
        </div>
    </div>

    <Form
        v-bind="store.form()"
        reset-on-success
        v-slot="{ errors, processing }"
        class="space-y-5"
    >
        <div class="grid gap-2">
            <Label htmlFor="password" class="text-sm font-medium">Password</Label>
            <PasswordInput
                id="password"
                name="password"
                required
                autocomplete="current-password"
                autofocus
                class="crm-input h-12"
            />
            <InputError :message="errors.password" />
        </div>

        <Button
            class="crm-button-primary h-12 w-full"
            :disabled="processing"
            data-test="confirm-password-button"
        >
            <Spinner v-if="processing" />
            Confirm password
        </Button>
    </Form>
</template>
