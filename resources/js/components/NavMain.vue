<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel class="px-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-sidebar-foreground/45">
            Workspace
        </SidebarGroupLabel>

        <SidebarMenu class="mt-2 gap-1">
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="rounded-2xl px-3 py-2.5 text-sidebar-foreground/72 transition hover:bg-white/7 hover:text-sidebar-foreground data-[active=true]:bg-white/8 data-[active=true]:text-sidebar-foreground data-[active=true]:shadow-[inset_0_1px_0_rgba(255,255,255,0.05)]"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" class="size-4" />
                        <span class="font-medium">{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
