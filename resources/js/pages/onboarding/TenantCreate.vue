<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Check } from 'lucide-vue-next';
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
                title: 'Workspace onboarding',
                href: '/onboarding/workspace',
            },
        ],
    },
});

interface Plan {
    name: string;
    slug: string;
    description: string | null;
    price_monthly: string | number;
    price_yearly: string | number;
    max_users: number | null;
}

const props = defineProps<{
    plans: Plan[];
    defaults: {
        timezone: string;
        currencyCode: string;
        phoneCountryCode: string;
        defaultCountry: string;
    };
}>();

const form = useForm({
    company_name: '',
    subscription_plan_slug: props.plans[0]?.slug ?? 'starter',
    timezone: props.defaults.timezone,
    currency_code: props.defaults.currencyCode,
    phone_country_code: props.defaults.phoneCountryCode,
    default_country: props.defaults.defaultCountry,
});

const submit = () => {
    form.post('/onboarding/workspace');
};
</script>

<template>
    <Head title="Create workspace" />

    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-4">
        <PageHeader
            eyebrow="Welcome to ClientDesk"
            title="Create your workspace"
            description="Set up your company workspace. You'll be the workspace admin, and default billing and dialing settings will be applied — you can change them later."
        />

        <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <form class="rounded-2xl border border-border bg-card p-6" @submit.prevent="submit">
                <div class="grid gap-5">
                    <div class="grid gap-2">
                        <Label for="company_name">Company name</Label>
                        <Input
                            id="company_name"
                            v-model="form.company_name"
                            type="text"
                            required
                            autofocus
                            placeholder="Acme Realty"
                            :aria-invalid="Boolean(form.errors.company_name)"
                        />
                        <InputError :message="form.errors.company_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="subscription_plan_slug">Plan</Label>
                        <SelectInput id="subscription_plan_slug" v-model="form.subscription_plan_slug">
                            <option v-for="plan in plans" :key="plan.slug" :value="plan.slug">
                                {{ plan.name }}
                            </option>
                        </SelectInput>
                        <InputError :message="form.errors.subscription_plan_slug" />
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="timezone">Timezone</Label>
                            <Input id="timezone" v-model="form.timezone" type="text" :aria-invalid="Boolean(form.errors.timezone)" />
                            <InputError :message="form.errors.timezone" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="currency_code">Currency code</Label>
                            <Input
                                id="currency_code"
                                v-model="form.currency_code"
                                type="text"
                                maxlength="3"
                                class="uppercase"
                                :aria-invalid="Boolean(form.errors.currency_code)"
                            />
                            <InputError :message="form.errors.currency_code" />
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="phone_country_code">Phone country code</Label>
                            <Input
                                id="phone_country_code"
                                v-model="form.phone_country_code"
                                type="text"
                                :aria-invalid="Boolean(form.errors.phone_country_code)"
                            />
                            <InputError :message="form.errors.phone_country_code" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="default_country">Default country</Label>
                            <Input
                                id="default_country"
                                v-model="form.default_country"
                                type="text"
                                maxlength="2"
                                class="uppercase"
                                :aria-invalid="Boolean(form.errors.default_country)"
                            />
                            <InputError :message="form.errors.default_country" />
                        </div>
                    </div>

                    <div class="pt-2">
                        <Button type="submit" :disabled="form.processing">
                            <Spinner v-if="form.processing" />
                            Create workspace
                        </Button>
                    </div>
                </div>
            </form>

            <div class="space-y-4">
                <button
                    v-for="plan in plans"
                    :key="plan.slug"
                    type="button"
                    class="w-full rounded-2xl border bg-card p-5 text-left transition"
                    :class="
                        form.subscription_plan_slug === plan.slug
                            ? 'border-primary ring-2 ring-primary/20'
                            : 'border-border hover:border-primary/40'
                    "
                    @click="form.subscription_plan_slug = plan.slug"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold">{{ plan.name }}</h2>
                            <p class="mt-2 text-sm leading-7 text-muted-foreground">
                                {{ plan.description || 'Built for growing sales operations.' }}
                            </p>
                        </div>
                        <span
                            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border transition"
                            :class="
                                form.subscription_plan_slug === plan.slug
                                    ? 'border-primary bg-primary text-primary-foreground'
                                    : 'border-border text-transparent'
                            "
                        >
                            <Check class="h-3.5 w-3.5" />
                        </span>
                    </div>

                    <div class="mt-4 flex flex-wrap items-baseline gap-x-4 gap-y-1 text-sm text-muted-foreground">
                        <span><span class="text-xl font-semibold text-foreground">${{ plan.price_monthly }}</span>/mo</span>
                        <span>${{ plan.price_yearly }}/yr</span>
                        <span>Up to {{ plan.max_users ?? 'unlimited' }} users</span>
                    </div>
                </button>
            </div>
        </section>
    </div>
</template>
