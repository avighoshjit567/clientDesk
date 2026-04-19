<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Reset password',
        description: 'Choose a new password to secure your ClientDesk account.',
    },
});

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <Head title="Reset password" />

    <Form
        v-bind="update.form()"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="space-y-5"
    >
        <div class="grid gap-2">
            <Label for="email" class="text-sm font-medium">Email</Label>
            <Input
                id="email"
                type="email"
                name="email"
                autocomplete="email"
                v-model="inputEmail"
                readonly
                class="crm-input h-12"
            />
            <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
            <Label for="password" class="text-sm font-medium">Password</Label>
            <PasswordInput
                id="password"
                name="password"
                autocomplete="new-password"
                autofocus
                placeholder="New password"
                class="crm-input h-12"
            />
            <InputError :message="errors.password" />
        </div>

        <div class="grid gap-2">
            <Label for="password_confirmation" class="text-sm font-medium">Confirm password</Label>
            <PasswordInput
                id="password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="Confirm password"
                class="crm-input h-12"
            />
            <InputError :message="errors.password_confirmation" />
        </div>

        <Button
            type="submit"
            class="crm-button-primary mt-2 h-12 w-full"
            :disabled="processing"
            data-test="reset-password-button"
        >
            <Spinner v-if="processing" />
            Reset password
        </Button>
    </Form>
</template>
