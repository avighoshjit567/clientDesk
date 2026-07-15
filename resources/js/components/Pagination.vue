<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

defineProps<{
    links: PaginationLink[];
}>();
</script>

<template>
    <nav v-if="links.length > 3" class="mt-5 flex flex-wrap items-center justify-center gap-1" aria-label="Pagination">
        <template v-for="(link, index) in links" :key="index">
            <Link
                v-if="link.url"
                :href="link.url"
                preserve-scroll
                class="flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition"
                :class="
                    link.active
                        ? 'border-primary bg-primary font-semibold text-primary-foreground'
                        : 'border-border text-muted-foreground hover:border-primary/40 hover:text-foreground'
                "
                v-html="link.label"
            />
            <span
                v-else
                class="flex h-9 min-w-9 items-center justify-center rounded-md border border-border px-3 text-sm text-muted-foreground/50"
                v-html="link.label"
            />
        </template>
    </nav>
</template>
