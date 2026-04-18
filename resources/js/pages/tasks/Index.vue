<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Auth } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tasks',
                href: '/tasks',
            },
        ],
    },
});

interface NamedOption {
    id: number;
    name: string;
}

interface TaskRow {
    id: number;
    title: string;
    status: string | null;
    priority: string | null;
    dueAt: string | null;
    leadName: string | null;
    assignedTo: string | null;
}

interface FollowUpRow {
    id: number;
    scheduledFor: string | null;
    status: string | null;
    leadName: string | null;
    taskTitle: string | null;
    assignedTo: string | null;
    notes: string | null;
}

const props = defineProps<{
    tasks: TaskRow[];
    followUps: FollowUpRow[];
    teamMembers: NamedOption[];
    leads: NamedOption[];
    taskStatusOptions: string[];
    taskPriorityOptions: string[];
    followUpStatusOptions: string[];
    stats: {
        totalTasks: number;
        pendingTasks: number;
        inProgressTasks: number;
        pendingFollowUps: number;
    };
}>();

const page = usePage<{ auth: Auth }>();
const permissions = computed(() => page.props.auth?.context?.permissions ?? []);
const canCreateTasks = computed(() => permissions.value.includes('tasks.create'));
const canCreateFollowUps = computed(() => permissions.value.includes('followups.create'));

const taskForm = useForm({
    title: '',
    description: '',
    status: props.taskStatusOptions[0] ?? 'pending',
    priority: props.taskPriorityOptions[1] ?? props.taskPriorityOptions[0] ?? 'medium',
    due_at: '',
    lead_id: '',
    assigned_to_user_id: '',
});

const followUpForm = useForm({
    scheduled_for: '',
    status: props.followUpStatusOptions[0] ?? 'pending',
    notes: '',
    lead_id: '',
    task_id: '',
    assigned_to_user_id: '',
});

const submitTask = () => {
    taskForm.transform((data) => ({
        ...data,
        lead_id: data.lead_id || null,
        assigned_to_user_id: data.assigned_to_user_id || null,
        due_at: data.due_at || null,
    })).post('/tasks', {
        preserveScroll: true,
        onSuccess: () => taskForm.reset('title', 'description', 'due_at'),
    });
};

const submitFollowUp = () => {
    followUpForm.transform((data) => ({
        ...data,
        lead_id: data.lead_id || null,
        task_id: data.task_id || null,
        assigned_to_user_id: data.assigned_to_user_id || null,
    })).post('/follow-ups', {
        preserveScroll: true,
        onSuccess: () => followUpForm.reset('scheduled_for', 'notes'),
    });
};
</script>

<template>
    <Head title="Tasks" />

    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
        <section class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <p class="text-sm font-medium text-orange-500">Phase 2 CRM operations</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Tasks and follow-ups</h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 text-muted-foreground">
                Manage day-to-day sales execution, assign work to teammates, and schedule follow-up actions for leads.
            </p>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Total tasks</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.totalTasks }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Pending</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.pendingTasks }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">In progress</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.inProgressTasks }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Pending follow-ups</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.pendingFollowUps }}</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <form v-if="canCreateTasks" class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border" @submit.prevent="submitTask">
                    <h2 class="text-lg font-semibold">Create task</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Add an actionable item for a teammate or lead workflow.</p>

                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Title</label>
                            <input v-model="taskForm.title" type="text" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500" />
                            <p v-if="taskForm.errors.title" class="mt-2 text-sm text-red-500">{{ taskForm.errors.title }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Status</label>
                            <select v-model="taskForm.status" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option v-for="status in taskStatusOptions" :key="status" :value="status">{{ status }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Priority</label>
                            <select v-model="taskForm.priority" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option v-for="priority in taskPriorityOptions" :key="priority" :value="priority">{{ priority }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Due at</label>
                            <input v-model="taskForm.due_at" type="datetime-local" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500" />
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Lead</label>
                            <select v-model="taskForm.lead_id" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option value="">None</option>
                                <option v-for="lead in leads" :key="lead.id" :value="String(lead.id)">{{ lead.name }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Assign to</label>
                            <select v-model="taskForm.assigned_to_user_id" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option value="">Unassigned</option>
                                <option v-for="member in teamMembers" :key="member.id" :value="String(member.id)">{{ member.name }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Description</label>
                            <textarea v-model="taskForm.description" rows="4" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" :disabled="taskForm.processing" class="inline-flex items-center rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-orange-400 disabled:cursor-not-allowed disabled:opacity-70">
                            {{ taskForm.processing ? 'Saving...' : 'Create task' }}
                        </button>
                    </div>
                </form>

                <div v-else class="rounded-2xl border border-dashed border-sidebar-border/70 bg-background p-6 text-sm text-muted-foreground dark:border-sidebar-border">
                    Your current role can view tasks, but cannot create new tasks.
                </div>

                <div class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
                    <h2 class="text-lg font-semibold">Task list</h2>
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border">
                                <tr>
                                    <th class="px-3 py-3 font-medium">Title</th>
                                    <th class="px-3 py-3 font-medium">Status</th>
                                    <th class="px-3 py-3 font-medium">Priority</th>
                                    <th class="px-3 py-3 font-medium">Lead</th>
                                    <th class="px-3 py-3 font-medium">Assigned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="task in tasks" :key="task.id" class="border-b border-sidebar-border/40 dark:border-sidebar-border/60">
                                    <td class="px-3 py-3">
                                        <div class="font-medium">{{ task.title }}</div>
                                        <div class="text-xs text-muted-foreground">{{ task.dueAt || 'No due date' }}</div>
                                    </td>
                                    <td class="px-3 py-3">{{ task.status }}</td>
                                    <td class="px-3 py-3">{{ task.priority }}</td>
                                    <td class="px-3 py-3">{{ task.leadName || 'None' }}</td>
                                    <td class="px-3 py-3">{{ task.assignedTo || 'Unassigned' }}</td>
                                </tr>
                                <tr v-if="tasks.length === 0">
                                    <td colspan="5" class="px-3 py-8 text-center text-muted-foreground">No tasks yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <form v-if="canCreateFollowUps" class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border" @submit.prevent="submitFollowUp">
                    <h2 class="text-lg font-semibold">Schedule follow-up</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Set the next touchpoint for a lead or task.</p>

                    <div class="mt-5 grid gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Scheduled for</label>
                            <input v-model="followUpForm.scheduled_for" type="datetime-local" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500" />
                            <p v-if="followUpForm.errors.scheduled_for" class="mt-2 text-sm text-red-500">{{ followUpForm.errors.scheduled_for }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Status</label>
                            <select v-model="followUpForm.status" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option v-for="status in followUpStatusOptions" :key="status" :value="status">{{ status }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Lead</label>
                            <select v-model="followUpForm.lead_id" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option value="">None</option>
                                <option v-for="lead in leads" :key="lead.id" :value="String(lead.id)">{{ lead.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Related task</label>
                            <select v-model="followUpForm.task_id" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option value="">None</option>
                                <option v-for="task in tasks" :key="task.id" :value="String(task.id)">{{ task.title }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Assign to</label>
                            <select v-model="followUpForm.assigned_to_user_id" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option value="">Unassigned</option>
                                <option v-for="member in teamMembers" :key="member.id" :value="String(member.id)">{{ member.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Notes</label>
                            <textarea v-model="followUpForm.notes" rows="4" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" :disabled="followUpForm.processing" class="inline-flex items-center rounded-lg border border-sidebar-border/70 px-5 py-2.5 text-sm font-semibold transition hover:border-orange-500 dark:border-sidebar-border disabled:cursor-not-allowed disabled:opacity-70">
                            {{ followUpForm.processing ? 'Saving...' : 'Create follow-up' }}
                        </button>
                    </div>
                </form>

                <div v-else class="rounded-2xl border border-dashed border-sidebar-border/70 bg-background p-6 text-sm text-muted-foreground dark:border-sidebar-border">
                    Your current role can view follow-ups, but cannot schedule them.
                </div>

                <div class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
                    <h2 class="text-lg font-semibold">Upcoming follow-ups</h2>
                    <div class="mt-5 space-y-3">
                        <div v-for="followUp in followUps" :key="followUp.id" class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-medium">{{ followUp.leadName || followUp.taskTitle || 'General follow-up' }}</div>
                                    <div class="mt-1 text-sm text-muted-foreground">{{ followUp.scheduledFor }} • {{ followUp.assignedTo || 'Unassigned' }}</div>
                                </div>
                                <span class="rounded-full bg-orange-500/10 px-2.5 py-1 text-xs font-medium text-orange-600 dark:text-orange-300">{{ followUp.status }}</span>
                            </div>
                            <div class="mt-2 text-sm text-muted-foreground">{{ followUp.notes || 'No notes yet.' }}</div>
                        </div>
                        <div v-if="followUps.length === 0" class="rounded-xl border border-dashed border-sidebar-border/70 p-4 text-sm text-muted-foreground dark:border-sidebar-border">
                            No follow-ups scheduled yet.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
