<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { UserPlus } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineOptions({
    layout: {
        title: 'Create your account',
        description: 'Start with a clean account setup, then create or join your ClientDesk workspace.',
    },
});
</script>

<template>
    <Head title="Register" />

    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-border/60 bg-muted/50 p-4">
        <div class="flex size-10 items-center justify-center rounded-2xl bg-orange-500/10 text-orange-500">
            <UserPlus class="size-5" />
        </div>
        <div>
            <p class="text-sm font-medium text-foreground">Get started the right way</p>
            <p class="mt-1 text-sm leading-6 text-muted-foreground">
                Create your account first, then continue into onboarding or accept an invitation.
            </p>
        </div>
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-5">
            <div class="grid gap-2">
                <Label for="name" class="text-sm font-medium">Name</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Full name"
                    class="crm-input h-12"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email" class="text-sm font-medium">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                    class="crm-input h-12"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password" class="text-sm font-medium">Password</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Password"
                    class="crm-input h-12"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation" class="text-sm font-medium">Confirm password</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    class="crm-input h-12"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="crm-button-primary mt-2 h-12 w-full"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Create account
            </Button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <TextLink :href="login()" :tabindex="6">Log in</TextLink>
        </div>
    </Form>
</template>
