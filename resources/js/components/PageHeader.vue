<script setup lang="ts">
defineProps<{
    eyebrow?: string;
    title: string;
    description?: string;
}>();
</script>

<template>
    <section class="rounded-2xl border border-border bg-card p-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div>
                <p v-if="eyebrow" class="text-sm font-medium text-primary">{{ eyebrow }}</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">{{ title }}</h1>
                <p v-if="description" class="mt-2 max-w-3xl text-sm leading-7 text-muted-foreground">
                    {{ description }}
                </p>
            </div>
            <slot name="aside" />
        </div>
    </section>
</template>
