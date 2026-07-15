<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    status: string | null;
}>();

const colorMap: Record<string, string> = {
    new: 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
    contacted: 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300',
    in_progress: 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300',
    qualified: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
    completed: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
    done: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
    active: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
    won: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
    pending: 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300',
    suspended: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
    cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
    lost: 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
};

const classes = computed(
    () => colorMap[props.status ?? ''] ?? 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300',
);

const label = computed(() => (props.status ?? 'unknown').replaceAll('_', ' '));
</script>

<template>
    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize" :class="classes">
        {{ label }}
    </span>
</template>
