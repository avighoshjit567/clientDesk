<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import {
    ArrowRight,
    CalendarCheck,
    Check,
    MapPin,
    PhoneCall,
    ShieldCheck,
    TrendingUp,
    Users,
} from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const features = [
    {
        icon: Users,
        title: 'Lead management',
        description: 'Capture, organize, and qualify every lead from every source in one shared workspace.',
    },
    {
        icon: CalendarCheck,
        title: 'Follow-ups & tasks',
        description: 'Never miss a callback. Schedule follow-ups and assign tasks so nothing slips through.',
    },
    {
        icon: TrendingUp,
        title: 'Deal pipeline',
        description: 'Move deals through clear stages and see exactly where every opportunity stands.',
    },
    {
        icon: MapPin,
        title: 'Site visits',
        description: 'Plan property visits, track outcomes, and connect them back to the right lead.',
    },
    {
        icon: PhoneCall,
        title: 'Call logs & notes',
        description: 'Log every call and conversation so the full client history is always at hand.',
    },
    {
        icon: ShieldCheck,
        title: 'Team & roles',
        description: 'Invite your team with role-based access — admins, managers, and sales agents.',
    },
];

const steps = [
    {
        title: 'Create your workspace',
        description: 'Set up your company workspace in under a minute. No credit card required.',
    },
    {
        title: 'Invite your team',
        description: 'Add agents and managers with the right permissions from day one.',
    },
    {
        title: 'Track every lead to close',
        description: 'Work leads, schedule visits, and move deals through your pipeline.',
    },
];

const highlights = ['Built for real estate teams', 'Role-based team access', 'Free to get started'];
</script>

<template>
    <Head title="ClientDesk — CRM for real estate and sales teams" />

    <div class="min-h-screen bg-white text-zinc-900 antialiased">
        <!-- Navbar -->
        <header class="sticky top-0 z-40 border-b border-zinc-100 bg-white/80 backdrop-blur">
            <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white">C</span>
                    <span class="text-lg font-semibold tracking-tight">ClientDesk</span>
                </Link>

                <nav class="flex items-center gap-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 transition hover:text-zinc-900"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600"
                        >
                            Get started
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main>
            <!-- Hero -->
            <section class="relative overflow-hidden">
                <div
                    class="pointer-events-none absolute inset-x-0 top-0 h-[480px] bg-[radial-gradient(60%_60%_at_50%_0%,rgba(249,115,22,0.08),transparent)]"
                    aria-hidden="true"
                />

                <div class="mx-auto w-full max-w-7xl px-6 pt-20 pb-16 text-center lg:px-8 lg:pt-28">
                    <p
                        class="mx-auto inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-4 py-1.5 text-sm font-medium text-orange-700"
                    >
                        CRM for real estate & sales teams
                    </p>

                    <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-semibold tracking-tight text-zinc-900 sm:text-5xl lg:text-6xl">
                        Close more deals with a CRM built for
                        <span class="text-orange-500">real estate teams</span>
                    </h1>

                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-zinc-600">
                        ClientDesk brings your leads, follow-ups, site visits, and deal pipeline into one simple
                        workspace — so your team spends less time organizing and more time selling.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <Link
                            v-if="canRegister && !$page.props.auth.user"
                            :href="register()"
                            class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600"
                        >
                            Get started free
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="login()"
                            class="rounded-lg border border-zinc-200 px-6 py-3 text-sm font-semibold text-zinc-700 transition hover:border-zinc-300 hover:bg-zinc-50"
                        >
                            Sign in
                        </Link>
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600"
                        >
                            Open dashboard
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>

                    <ul class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-zinc-500">
                        <li v-for="highlight in highlights" :key="highlight" class="flex items-center gap-1.5">
                            <Check class="h-4 w-4 text-orange-500" />
                            {{ highlight }}
                        </li>
                    </ul>

                    <!-- Dashboard preview -->
                    <div class="relative mx-auto mt-16 max-w-5xl">
                        <div
                            class="pointer-events-none absolute -inset-x-8 -top-8 h-40 bg-[radial-gradient(50%_100%_at_50%_0%,rgba(249,115,22,0.10),transparent)]"
                            aria-hidden="true"
                        />
                        <div class="relative overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-xl shadow-zinc-900/5">
                            <!-- Browser bar -->
                            <div class="flex items-center gap-1.5 border-b border-zinc-100 bg-zinc-50 px-4 py-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-zinc-200" />
                                <span class="h-2.5 w-2.5 rounded-full bg-zinc-200" />
                                <span class="h-2.5 w-2.5 rounded-full bg-zinc-200" />
                                <span class="ml-3 hidden rounded-md bg-white px-3 py-1 text-xs text-zinc-400 sm:block">
                                    app.clientdesk.test/dashboard
                                </span>
                            </div>

                            <div class="flex text-left">
                                <!-- Sidebar -->
                                <div class="hidden w-44 shrink-0 border-r border-zinc-100 p-4 sm:block">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-md bg-orange-500 text-[10px] font-bold text-white">C</span>
                                        <span class="text-xs font-semibold">ClientDesk</span>
                                    </div>
                                    <div class="mt-5 space-y-1">
                                        <div class="rounded-md bg-orange-50 px-2.5 py-1.5 text-xs font-medium text-orange-600">Dashboard</div>
                                        <div class="px-2.5 py-1.5 text-xs text-zinc-500">Leads</div>
                                        <div class="px-2.5 py-1.5 text-xs text-zinc-500">Tasks</div>
                                        <div class="px-2.5 py-1.5 text-xs text-zinc-500">Deals</div>
                                        <div class="px-2.5 py-1.5 text-xs text-zinc-500">Site visits</div>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 p-4 sm:p-6">
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="rounded-xl border border-zinc-100 p-3 sm:p-4">
                                            <p class="text-[11px] text-zinc-500">New leads</p>
                                            <p class="mt-1 text-lg font-semibold sm:text-xl">128</p>
                                            <p class="mt-0.5 text-[11px] font-medium text-emerald-600">+12% this week</p>
                                        </div>
                                        <div class="rounded-xl border border-zinc-100 p-3 sm:p-4">
                                            <p class="text-[11px] text-zinc-500">Follow-ups due</p>
                                            <p class="mt-1 text-lg font-semibold sm:text-xl">24</p>
                                            <p class="mt-0.5 text-[11px] font-medium text-orange-600">8 due today</p>
                                        </div>
                                        <div class="rounded-xl border border-zinc-100 p-3 sm:p-4">
                                            <p class="text-[11px] text-zinc-500">Deals in pipeline</p>
                                            <p class="mt-1 text-lg font-semibold sm:text-xl">$1.2M</p>
                                            <p class="mt-0.5 text-[11px] font-medium text-emerald-600">17 active</p>
                                        </div>
                                    </div>

                                    <div class="mt-4 rounded-xl border border-zinc-100">
                                        <div class="border-b border-zinc-100 px-4 py-2.5 text-xs font-semibold text-zinc-700">Recent leads</div>
                                        <div class="divide-y divide-zinc-50">
                                            <div class="flex items-center justify-between px-4 py-2.5">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-orange-100 text-[10px] font-semibold text-orange-700">SR</span>
                                                    <span class="text-xs text-zinc-700">Sara Rahman</span>
                                                </div>
                                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700">Qualified</span>
                                            </div>
                                            <div class="flex items-center justify-between px-4 py-2.5">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-sky-100 text-[10px] font-semibold text-sky-700">JM</span>
                                                    <span class="text-xs text-zinc-700">James Miller</span>
                                                </div>
                                                <span class="rounded-full bg-orange-50 px-2 py-0.5 text-[10px] font-medium text-orange-700">Follow-up</span>
                                            </div>
                                            <div class="flex items-center justify-between px-4 py-2.5">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-violet-100 text-[10px] font-semibold text-violet-700">AK</span>
                                                    <span class="text-xs text-zinc-700">Ayesha Khan</span>
                                                </div>
                                                <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-medium text-zinc-600">New</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section class="border-t border-zinc-100 bg-zinc-50/60">
                <div class="mx-auto w-full max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2 class="text-3xl font-semibold tracking-tight text-zinc-900 sm:text-4xl">
                            Everything your sales team needs
                        </h2>
                        <p class="mt-4 text-lg leading-8 text-zinc-600">
                            One workspace for the entire journey — from first inquiry to closed deal.
                        </p>
                    </div>

                    <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="feature in features"
                            :key="feature.title"
                            class="rounded-2xl border border-zinc-200 bg-white p-6 transition hover:border-orange-200 hover:shadow-sm"
                        >
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-600">
                                <component :is="feature.icon" class="h-5 w-5" />
                            </span>
                            <h3 class="mt-4 text-base font-semibold text-zinc-900">{{ feature.title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-zinc-600">{{ feature.description }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How it works -->
            <section>
                <div class="mx-auto w-full max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2 class="text-3xl font-semibold tracking-tight text-zinc-900 sm:text-4xl">
                            Up and running in minutes
                        </h2>
                        <p class="mt-4 text-lg leading-8 text-zinc-600">
                            No setup calls, no onboarding fees. Just sign up and start working your leads.
                        </p>
                    </div>

                    <ol class="mt-14 grid gap-8 sm:grid-cols-3">
                        <li v-for="(step, index) in steps" :key="step.title" class="text-center">
                            <span
                                class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-orange-500 text-sm font-bold text-white"
                            >
                                {{ index + 1 }}
                            </span>
                            <h3 class="mt-4 text-base font-semibold text-zinc-900">{{ step.title }}</h3>
                            <p class="mx-auto mt-2 max-w-xs text-sm leading-6 text-zinc-600">{{ step.description }}</p>
                        </li>
                    </ol>
                </div>
            </section>

            <!-- CTA band -->
            <section class="px-6 pb-20 lg:px-8 lg:pb-24">
                <div class="mx-auto max-w-7xl overflow-hidden rounded-3xl bg-zinc-900 px-6 py-16 text-center sm:px-16">
                    <h2 class="mx-auto max-w-2xl text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                        Ready to organize your sales?
                    </h2>
                    <p class="mx-auto mt-4 max-w-xl text-lg leading-8 text-zinc-300">
                        Create your workspace today and give your team a single place to win deals.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <Link
                            v-if="canRegister && !$page.props.auth.user"
                            :href="register()"
                            class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-600"
                        >
                            Get started free
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-600"
                        >
                            Open dashboard
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-zinc-100">
            <div class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-4 px-6 py-8 sm:flex-row lg:px-8">
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-md bg-orange-500 text-[10px] font-bold text-white">C</span>
                    <span class="text-sm font-semibold text-zinc-900">ClientDesk</span>
                    <span class="text-sm text-zinc-400">— CRM for real estate & sales teams</span>
                </div>
                <div class="flex items-center gap-4 text-sm text-zinc-500">
                    <Link :href="login()" class="transition hover:text-zinc-900">Log in</Link>
                    <Link v-if="canRegister" :href="register()" class="transition hover:text-zinc-900">Get started</Link>
                    <span>&copy; {{ new Date().getFullYear() }} ClientDesk</span>
                </div>
            </div>
        </footer>
    </div>
</template>
