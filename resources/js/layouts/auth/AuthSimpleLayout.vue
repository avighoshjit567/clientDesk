<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-svh flex-col items-center justify-center bg-white p-6 md:p-10">
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-[420px] bg-[radial-gradient(60%_60%_at_50%_0%,rgba(249,115,22,0.08),transparent)]"
            aria-hidden="true"
        />

        <div class="relative w-full max-w-md">
            <div class="flex flex-col items-center gap-4">
                <Link :href="home()" class="flex flex-col items-center gap-3 font-medium">
                    <AppLogoIcon class="size-11" />
                    <span class="text-lg font-semibold tracking-tight text-zinc-900">ClientDesk</span>
                </Link>
                <div class="space-y-1.5 text-center">
                    <h1 class="text-xl font-semibold tracking-tight text-zinc-900">{{ title }}</h1>
                    <p class="text-sm text-zinc-500">
                        {{ description }}
                    </p>
                </div>
            </div>

            <div class="mt-8 rounded-2xl border border-zinc-200 bg-white p-6 shadow-xl shadow-zinc-900/5 sm:p-8">
                <slot />
            </div>

            <p class="mt-6 text-center text-sm text-zinc-500">
                <Link :href="home()" class="transition hover:text-zinc-900">&larr; Back to home</Link>
            </p>
        </div>
    </div>
</template>
