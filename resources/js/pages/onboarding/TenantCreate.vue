<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Globe2, Rocket, ShieldCheck } from 'lucide-vue-next';

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

    <div class="crm-page max-w-7xl">
        <section class="crm-hero">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="crm-eyebrow">Workspace onboarding</p>
                    <h1 class="crm-title">Create your first ClientDesk workspace</h1>
                    <p class="crm-subtitle">
                        Set up the company workspace, attach yourself as tenant admin, and start from a cleaner, more production-looking panel.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 xl:w-[420px]">
                    <div class="crm-card-soft">
                        <Rocket class="size-5 text-orange-500" />
                        <p class="mt-3 text-sm font-semibold text-foreground">Fast start</p>
                        <p class="mt-2 text-sm text-muted-foreground">Spin up the workspace with sane default sales settings.</p>
                    </div>
                    <div class="crm-card-soft">
                        <ShieldCheck class="size-5 text-orange-500" />
                        <p class="mt-3 text-sm font-semibold text-foreground">Role aware</p>
                        <p class="mt-2 text-sm text-muted-foreground">Your account becomes the first tenant admin automatically.</p>
                    </div>
                    <div class="crm-card-soft">
                        <Globe2 class="size-5 text-orange-500" />
                        <p class="mt-3 text-sm font-semibold text-foreground">Ready to scale</p>
                        <p class="mt-2 text-sm text-muted-foreground">Timezone, currency, and dialing defaults are set up day one.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.08fr_0.92fr]">
            <form class="crm-card" @submit.prevent="submit">
                <div class="grid gap-5">
                    <div>
                        <label for="company_name" class="crm-label">Company name</label>
                        <input id="company_name" v-model="form.company_name" type="text" class="crm-input" placeholder="Avijit Realty" />
                        <p v-if="form.errors.company_name" class="crm-error">{{ form.errors.company_name }}</p>
                    </div>

                    <div>
                        <label for="subscription_plan_slug" class="crm-label">Plan</label>
                        <select id="subscription_plan_slug" v-model="form.subscription_plan_slug" class="crm-select">
                            <option v-for="plan in props.plans" :key="plan.slug" :value="plan.slug">
                                {{ plan.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.subscription_plan_slug" class="crm-error">
                            {{ form.errors.subscription_plan_slug }}
                        </p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="timezone" class="crm-label">Timezone</label>
                            <input id="timezone" v-model="form.timezone" type="text" class="crm-input" />
                            <p v-if="form.errors.timezone" class="crm-error">{{ form.errors.timezone }}</p>
                        </div>
                        <div>
                            <label for="currency_code" class="crm-label">Currency code</label>
                            <input id="currency_code" v-model="form.currency_code" type="text" maxlength="3" class="crm-input uppercase" />
                            <p v-if="form.errors.currency_code" class="crm-error">{{ form.errors.currency_code }}</p>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="phone_country_code" class="crm-label">Phone country code</label>
                            <input id="phone_country_code" v-model="form.phone_country_code" type="text" class="crm-input" />
                            <p v-if="form.errors.phone_country_code" class="crm-error">
                                {{ form.errors.phone_country_code }}
                            </p>
                        </div>
                        <div>
                            <label for="default_country" class="crm-label">Default country</label>
                            <input id="default_country" v-model="form.default_country" type="text" maxlength="2" class="crm-input uppercase" />
                            <p v-if="form.errors.default_country" class="crm-error">{{ form.errors.default_country }}</p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="form.processing" class="crm-button-primary">
                            {{ form.processing ? 'Creating workspace...' : 'Create workspace' }}
                        </button>
                    </div>
                </div>
            </form>

            <div class="space-y-4">
                <div
                    v-for="plan in props.plans"
                    :key="plan.slug"
                    class="crm-card"
                    :class="form.subscription_plan_slug === plan.slug ? 'ring-2 ring-orange-400/30' : ''"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-foreground">{{ plan.name }}</h2>
                            <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                {{ plan.description || 'Built for growing sales operations.' }}
                            </p>
                        </div>
                        <button type="button" class="crm-button-secondary !px-3 !py-1.5 text-xs" @click="form.subscription_plan_slug = plan.slug">
                            Select
                        </button>
                    </div>

                    <div class="mt-4 grid gap-3 text-sm text-muted-foreground sm:grid-cols-3">
                        <div>
                            <p class="font-medium text-foreground">Monthly</p>
                            <p class="mt-1">{{ plan.price_monthly }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-foreground">Yearly</p>
                            <p class="mt-1">{{ plan.price_yearly }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-foreground">Max users</p>
                            <p class="mt-1">{{ plan.max_users ?? 'Custom' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
