<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
    label: string;
    value: string | number;
    hint?: string;
    icon?: Component;
}>();
</script>

<template>
    <div class="rounded-2xl border border-border bg-card p-5">
        <div class="flex items-center justify-between gap-3">
            <p class="text-sm text-muted-foreground">{{ label }}</p>
            <span v-if="icon" class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                <component :is="icon" class="h-4 w-4" />
            </span>
        </div>
        <p class="mt-2 text-3xl font-semibold tracking-tight">{{ value }}</p>
        <p v-if="hint" class="mt-2 text-sm text-muted-foreground">{{ hint }}</p>
    </div>
</template>
