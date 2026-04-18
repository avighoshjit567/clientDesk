<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

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
        <section class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <p class="text-sm font-medium text-orange-500">Phase 1 onboarding</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight">Create your first tenant workspace</h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 text-muted-foreground">
                This sets up the company workspace, links your user as the tenant admin, and applies the default
                billing and dialing settings for ClientDesk.
            </p>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <form
                class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border"
                @submit.prevent="submit"
            >
                <div class="grid gap-5">
                    <div>
                        <label for="company_name" class="mb-2 block text-sm font-medium">Company name</label>
                        <input
                            id="company_name"
                            v-model="form.company_name"
                            type="text"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none ring-0 transition focus:border-orange-500"
                            placeholder="Avijit Realty"
                        />
                        <p v-if="form.errors.company_name" class="mt-2 text-sm text-red-500">
                            {{ form.errors.company_name }}
                        </p>
                    </div>

                    <div>
                        <label for="subscription_plan_slug" class="mb-2 block text-sm font-medium">Plan</label>
                        <select
                            id="subscription_plan_slug"
                            v-model="form.subscription_plan_slug"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500"
                        >
                            <option v-for="plan in plans" :key="plan.slug" :value="plan.slug">
                                {{ plan.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.subscription_plan_slug" class="mt-2 text-sm text-red-500">
                            {{ form.errors.subscription_plan_slug }}
                        </p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="timezone" class="mb-2 block text-sm font-medium">Timezone</label>
                            <input
                                id="timezone"
                                v-model="form.timezone"
                                type="text"
                                class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500"
                            />
                            <p v-if="form.errors.timezone" class="mt-2 text-sm text-red-500">{{ form.errors.timezone }}</p>
                        </div>

                        <div>
                            <label for="currency_code" class="mb-2 block text-sm font-medium">Currency code</label>
                            <input
                                id="currency_code"
                                v-model="form.currency_code"
                                type="text"
                                maxlength="3"
                                class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm uppercase outline-none transition focus:border-orange-500"
                            />
                            <p v-if="form.errors.currency_code" class="mt-2 text-sm text-red-500">{{ form.errors.currency_code }}</p>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="phone_country_code" class="mb-2 block text-sm font-medium">Phone country code</label>
                            <input
                                id="phone_country_code"
                                v-model="form.phone_country_code"
                                type="text"
                                class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500"
                            />
                            <p v-if="form.errors.phone_country_code" class="mt-2 text-sm text-red-500">
                                {{ form.errors.phone_country_code }}
                            </p>
                        </div>

                        <div>
                            <label for="default_country" class="mb-2 block text-sm font-medium">Default country</label>
                            <input
                                id="default_country"
                                v-model="form.default_country"
                                type="text"
                                maxlength="2"
                                class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm uppercase outline-none transition focus:border-orange-500"
                            />
                            <p v-if="form.errors.default_country" class="mt-2 text-sm text-red-500">
                                {{ form.errors.default_country }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-orange-400 disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            {{ form.processing ? 'Creating workspace...' : 'Create workspace' }}
                        </button>
                    </div>
                </div>
            </form>

            <div class="space-y-4">
                <div
                    v-for="plan in plans"
                    :key="plan.slug"
                    class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border"
                    :class="form.subscription_plan_slug === plan.slug ? 'border-orange-500 ring-1 ring-orange-500/20' : ''"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold">{{ plan.name }}</h2>
                            <p class="mt-2 text-sm leading-7 text-muted-foreground">
                                {{ plan.description || 'Built for growing sales operations.' }}
                            </p>
                        </div>
                        <button
                            type="button"
                            class="rounded-md border border-sidebar-border/70 px-3 py-1.5 text-xs font-medium dark:border-sidebar-border"
                            @click="form.subscription_plan_slug = plan.slug"
                        >
                            Select
                        </button>
                    </div>

                    <div class="mt-4 text-sm text-muted-foreground">
                        <p><span class="font-medium text-foreground">Monthly:</span> {{ plan.price_monthly }}</p>
                        <p class="mt-1"><span class="font-medium text-foreground">Yearly:</span> {{ plan.price_yearly }}</p>
                        <p class="mt-1"><span class="font-medium text-foreground">Max users:</span> {{ plan.max_users ?? 'Custom' }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
