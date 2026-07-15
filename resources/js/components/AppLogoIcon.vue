<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 64 64"
        fill="none"
        :class="className"
        v-bind="$attrs"
    >
        <rect width="64" height="64" rx="14" fill="#F97316" />
        <path
            d="M 41.19 22.81 A 13 13 0 1 0 41.19 41.19"
            stroke="#FFFFFF"
            stroke-width="9"
            stroke-linecap="round"
            fill="none"
        />
    </svg>
</template>
