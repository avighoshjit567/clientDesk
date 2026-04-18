<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="ClientDesk" />

    <div class="min-h-screen bg-zinc-950 text-zinc-50">
        <header class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
            <div>
                <p class="text-lg font-semibold tracking-tight">ClientDesk</p>
                <p class="text-sm text-zinc-400">CRM for real estate and sales teams</p>
            </div>

            <nav class="flex items-center gap-3">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="rounded-md border border-zinc-700 px-4 py-2 text-sm font-medium text-zinc-100 transition hover:border-zinc-500"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="rounded-md px-4 py-2 text-sm font-medium text-zinc-200 transition hover:bg-zinc-900"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="rounded-md bg-orange-500 px-4 py-2 text-sm font-semibold text-zinc-950 transition hover:bg-orange-400"
                    >
                        Start now
                    </Link>
                </template>
            </nav>
        </header>

        <main class="mx-auto grid w-full max-w-7xl gap-10 px-6 py-12 lg:grid-cols-[1.2fr_0.8fr] lg:px-8 lg:py-20">
            <section class="space-y-8">
                <div class="inline-flex items-center rounded-full border border-orange-500/30 bg-orange-500/10 px-4 py-1.5 text-sm text-orange-200">
                    Phase 0 foundation is in progress
                </div>

                <div class="space-y-5">
                    <h1 class="max-w-4xl text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl">
                        A multi-tenant SaaS CRM for real estate companies and short-to-medium sales teams.
                    </h1>
                    <p class="max-w-2xl text-base leading-8 text-zinc-300 sm:text-lg">
                        ClientDesk is being built with Laravel, Inertia, Vue 3, Tailwind, shadcn-vue, and MySQL.
                        The product direction is focused on leads, follow-ups, calls, site visits, deal pipelines,
                        and tenant-aware team operations.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <Link
                        v-if="canRegister && !$page.props.auth.user"
                        :href="register()"
                        class="rounded-md bg-orange-500 px-5 py-3 text-sm font-semibold text-zinc-950 transition hover:bg-orange-400"
                    >
                        Create tenant workspace
                    </Link>
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="login()"
                        class="rounded-md border border-zinc-700 px-5 py-3 text-sm font-semibold text-zinc-100 transition hover:border-zinc-500"
                    >
                        Sign in
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-md bg-orange-500 px-5 py-3 text-sm font-semibold text-zinc-950 transition hover:bg-orange-400"
                    >
                        Open dashboard
                    </Link>
                </div>
            </section>

            <section class="grid gap-4">
                <div class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-6">
                    <p class="text-sm font-medium text-orange-200">Platform model</p>
                    <h2 class="mt-2 text-xl font-semibold text-white">Super admin plus tenant workspaces</h2>
                    <p class="mt-3 text-sm leading-7 text-zinc-300">
                        Platform-level super admin, tenant admins inside each company, and tenant-scoped sales users for
                        daily CRM operations.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-6">
                    <p class="text-sm font-medium text-orange-200">Initial modules</p>
                    <ul class="mt-3 space-y-2 text-sm leading-7 text-zinc-300">
                        <li>Leads and lead sources</li>
                        <li>Tasks and follow-ups</li>
                        <li>Call logs and notes</li>
                        <li>Projects, properties, and site visits</li>
                        <li>Deals and pipeline stages</li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-6">
                    <p class="text-sm font-medium text-orange-200">Development rules</p>
                    <p class="mt-3 text-sm leading-7 text-zinc-300">
                        Initial setup goes to main. After that, ClientDesk will be built phase by phase using focused
                        branches and pull requests.
                    </p>
                </div>
            </section>
        </main>
    </div>
</template>
