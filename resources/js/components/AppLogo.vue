<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div class="flex items-center gap-3">
        <div class="flex size-10 items-center justify-center rounded-2xl bg-linear-to-br from-orange-300 via-orange-400 to-orange-500 text-slate-950 shadow-lg shadow-orange-500/20 ring-1 ring-white/60 dark:ring-white/10">
            <AppLogoIcon class="size-5 fill-current" />
        </div>

        <div class="grid flex-1 text-left leading-tight">
            <span class="text-sm font-semibold tracking-tight text-current">
                ClientDesk
            </span>
            <span class="text-xs text-muted-foreground dark:text-sidebar-foreground/60">
                Sales workspace OS
            </span>
        </div>
    </div>
</template>
