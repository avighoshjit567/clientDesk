<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

withDefaults(
    defineProps<{
        title: string;
        description: string;
        confirmLabel?: string;
    }>(),
    {
        confirmLabel: 'Confirm',
    },
);

const emit = defineEmits<{
    (e: 'confirm'): void;
}>();
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>{{ description }}</DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button variant="secondary" type="button">Cancel</Button>
                </DialogClose>
                <DialogClose as-child>
                    <Button variant="destructive" type="button" @click="emit('confirm')">
                        {{ confirmLabel }}
                    </Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
