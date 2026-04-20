<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { CheckCircle2, ShieldCheck, Sparkles, Users } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import { home } from '@/routes';

const page = usePage();
const name = page.props.name;

defineProps<{
    title?: string;
    description?: string;
}>();

const highlights = [
    {
        title: 'Role-aware access',
        description: 'Super admin, tenant admin, and workspace roles stay clean from day one.',
        icon: ShieldCheck,
    },
    {
        title: 'Focused sales workflow',
        description: 'Leads, tasks, follow-ups, and team activity live in one organized shell.',
        icon: Users,
    },
    {
        title: 'Premium-ready foundation',
        description: 'A cleaner product surface now, with room for richer CRM workflows next.',
        icon: Sparkles,
    },
];
</script>

<template>
    <div class="relative min-h-svh overflow-hidden bg-background">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(251,146,60,0.16),transparent_26%),radial-gradient(circle_at_85%_10%,rgba(59,130,246,0.10),transparent_22%),linear-gradient(180deg,rgba(248,250,252,0.98),rgba(241,245,249,1))] dark:bg-[radial-gradient(circle_at_top_left,rgba(251,146,60,0.14),transparent_28%),radial-gradient(circle_at_85%_10%,rgba(96,165,250,0.10),transparent_24%),linear-gradient(180deg,rgba(15,23,42,0.96),rgba(2,6,23,1))]" />

        <div class="relative mx-auto grid min-h-svh max-w-7xl items-stretch px-4 py-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-6 lg:py-8">
            <div class="hidden flex-col justify-between rounded-[32px] border border-white/10 bg-slate-950 px-8 py-8 text-white shadow-2xl shadow-slate-950/30 lg:flex">
                <div>
                    <Link :href="home()" class="inline-flex items-center">
                        <AppLogo />
                    </Link>

                    <div class="mt-10 max-w-xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-orange-300/90">
                            {{ name }}
                        </p>
                        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white">
                            A sharper, more professional CRM experience from the first screen.
                        </h1>
                        <p class="mt-5 text-sm leading-7 text-slate-300">
                            Sign in to manage sales activity, workspace access, and team execution inside a cleaner ClientDesk product shell.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-4">
                        <div
                            v-for="item in highlights"
                            :key="item.title"
                            class="rounded-2xl border border-white/8 bg-white/5 p-4 backdrop-blur-sm"
                        >
                            <div class="flex items-start gap-3">
                                <div class="flex size-10 items-center justify-center rounded-2xl bg-orange-400/15 text-orange-300 ring-1 ring-white/10">
                                    <component :is="item.icon" class="size-5" />
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ item.title }}</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-300">
                                        {{ item.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/8 bg-white/5 p-4 text-sm text-slate-300">
                    <div class="flex items-center gap-2 text-white">
                        <CheckCircle2 class="size-4 text-emerald-300" />
                        Ready for team onboarding
                    </div>
                    <p class="mt-2 leading-6">
                        This is no longer the default starter auth shell. It now matches the upgraded ClientDesk panel styling.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-center px-0 py-8 lg:px-10">
                <div class="w-full max-w-xl">
                    <div class="mb-6 flex items-center justify-center lg:hidden">
                        <Link :href="home()" class="inline-flex items-center">
                            <AppLogo />
                        </Link>
                    </div>

                    <div class="crm-card rounded-[32px] p-6 sm:p-8">
                        <div class="space-y-3 text-center sm:text-left">
                            <p class="crm-eyebrow">ClientDesk access</p>
                            <h1 class="text-3xl font-semibold tracking-tight text-foreground">
                                {{ title }}
                            </h1>
                            <p class="text-sm leading-7 text-muted-foreground">
                                {{ description }}
                            </p>
                        </div>

                        <div class="mt-8">
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
