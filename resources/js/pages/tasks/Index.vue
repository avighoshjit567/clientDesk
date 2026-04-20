<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { CalendarClock, CircleDashed, ListChecks, Target, TimerReset } from 'lucide-vue-next';
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
    taskForm
        .transform((data) => ({
            ...data,
            lead_id: data.lead_id || null,
            assigned_to_user_id: data.assigned_to_user_id || null,
            due_at: data.due_at || null,
        }))
        .post('/tasks', {
            preserveScroll: true,
            onSuccess: () => taskForm.reset('title', 'description', 'due_at'),
        });
};

const submitFollowUp = () => {
    followUpForm
        .transform((data) => ({
            ...data,
            lead_id: data.lead_id || null,
            task_id: data.task_id || null,
            assigned_to_user_id: data.assigned_to_user_id || null,
        }))
        .post('/follow-ups', {
            preserveScroll: true,
            onSuccess: () => followUpForm.reset('scheduled_for', 'notes'),
        });
};

const statusClass = (status: string | null) => {
    switch ((status ?? '').toLowerCase()) {
        case 'completed':
            return 'crm-chip crm-chip-success';
        case 'in_progress':
        case 'in progress':
            return 'crm-chip crm-chip-warning';
        default:
            return 'crm-chip crm-chip-muted';
    }
};
</script>

<template>
    <Head title="Tasks" />

    <div class="crm-page">
        <section class="crm-hero">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="crm-eyebrow">Sales execution</p>
                    <h1 class="crm-title">Tasks and follow-ups</h1>
                    <p class="crm-subtitle">
                        Turn lead intent into visible execution. Assign work clearly, schedule next steps, and reduce missed handoffs.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="crm-chip crm-chip-success">{{ props.stats.totalTasks }} total tasks</span>
                        <span class="crm-chip crm-chip-muted">{{ props.followUps.length }} follow-up entries</span>
                        <span class="crm-chip crm-chip-muted">{{ props.teamMembers.length }} assignable teammates</span>
                    </div>
                </div>

                <div class="crm-card-soft max-w-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-muted-foreground">Execution principle</p>
                    <p class="mt-3 text-sm leading-6 text-muted-foreground">
                        Good CRM panels make ownership and timing obvious. This page is now built more like a real ops workspace.
                    </p>
                </div>
            </div>
        </section>

        <section class="crm-stat-grid">
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><ListChecks class="size-5" /></div>
                <p class="crm-stat-label">Total tasks</p>
                <p class="crm-stat-value">{{ props.stats.totalTasks }}</p>
                <p class="crm-stat-caption">All tracked execution items for the current workspace.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><CircleDashed class="size-5" /></div>
                <p class="crm-stat-label">Pending</p>
                <p class="crm-stat-value">{{ props.stats.pendingTasks }}</p>
                <p class="crm-stat-caption">Tasks waiting for the first push or owner response.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><Target class="size-5" /></div>
                <p class="crm-stat-label">In progress</p>
                <p class="crm-stat-value">{{ props.stats.inProgressTasks }}</p>
                <p class="crm-stat-caption">Work currently moving through the team.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><CalendarClock class="size-5" /></div>
                <p class="crm-stat-label">Pending follow-ups</p>
                <p class="crm-stat-value">{{ props.stats.pendingFollowUps }}</p>
                <p class="crm-stat-caption">Next touchpoints that still need scheduling discipline.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.08fr_0.92fr]">
            <div class="space-y-6">
                <form v-if="canCreateTasks" class="crm-card" @submit.prevent="submitTask">
                    <h2 class="crm-panel-title">Create task</h2>
                    <p class="crm-panel-copy">Add an actionable item for a teammate, lead, or daily sales workflow.</p>

                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="crm-label">Title</label>
                            <input v-model="taskForm.title" type="text" class="crm-input" />
                            <p v-if="taskForm.errors.title" class="crm-error">{{ taskForm.errors.title }}</p>
                        </div>
                        <div>
                            <label class="crm-label">Status</label>
                            <select v-model="taskForm.status" class="crm-select">
                                <option v-for="status in props.taskStatusOptions" :key="status" :value="status">
                                    {{ status }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="crm-label">Priority</label>
                            <select v-model="taskForm.priority" class="crm-select">
                                <option v-for="priority in props.taskPriorityOptions" :key="priority" :value="priority">
                                    {{ priority }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="crm-label">Due at</label>
                            <input v-model="taskForm.due_at" type="datetime-local" class="crm-input" />
                        </div>
                        <div>
                            <label class="crm-label">Lead</label>
                            <select v-model="taskForm.lead_id" class="crm-select">
                                <option value="">None</option>
                                <option v-for="lead in props.leads" :key="lead.id" :value="String(lead.id)">
                                    {{ lead.name }}
                                </option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="crm-label">Assign to</label>
                            <select v-model="taskForm.assigned_to_user_id" class="crm-select">
                                <option value="">Unassigned</option>
                                <option v-for="member in props.teamMembers" :key="member.id" :value="String(member.id)">
                                    {{ member.name }}
                                </option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="crm-label">Description</label>
                            <textarea v-model="taskForm.description" rows="4" class="crm-textarea"></textarea>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" :disabled="taskForm.processing" class="crm-button-primary">
                            {{ taskForm.processing ? 'Saving...' : 'Create task' }}
                        </button>
                    </div>
                </form>

                <div v-else class="crm-empty-state">
                    Your current role can view tasks, but cannot create new ones.
                </div>

                <article class="crm-card">
                    <h2 class="crm-panel-title">Task list</h2>
                    <p class="crm-panel-copy">The current workload across leads and teammates.</p>

                    <div class="crm-table-wrap">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Lead</th>
                                    <th>Assigned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="task in props.tasks" :key="task.id">
                                    <td>
                                        <div class="font-medium text-foreground">{{ task.title }}</div>
                                        <div class="mt-1 text-xs text-muted-foreground">{{ task.dueAt || 'No due date' }}</div>
                                    </td>
                                    <td>
                                        <span :class="statusClass(task.status)">
                                            {{ task.status || 'pending' }}
                                        </span>
                                    </td>
                                    <td>{{ task.priority || 'medium' }}</td>
                                    <td>{{ task.leadName || 'None' }}</td>
                                    <td>{{ task.assignedTo || 'Unassigned' }}</td>
                                </tr>
                                <tr v-if="props.tasks.length === 0">
                                    <td colspan="5">
                                        <div class="crm-empty-state">No tasks yet.</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <div class="space-y-6">
                <form v-if="canCreateFollowUps" class="crm-card" @submit.prevent="submitFollowUp">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="crm-panel-title">Schedule follow-up</h2>
                            <p class="crm-panel-copy">Set the next customer touchpoint before momentum drops.</p>
                        </div>
                        <TimerReset class="size-5 text-orange-500" />
                    </div>

                    <div class="mt-5 grid gap-4">
                        <div>
                            <label class="crm-label">Scheduled for</label>
                            <input v-model="followUpForm.scheduled_for" type="datetime-local" class="crm-input" />
                            <p v-if="followUpForm.errors.scheduled_for" class="crm-error">
                                {{ followUpForm.errors.scheduled_for }}
                            </p>
                        </div>
                        <div>
                            <label class="crm-label">Status</label>
                            <select v-model="followUpForm.status" class="crm-select">
                                <option v-for="status in props.followUpStatusOptions" :key="status" :value="status">
                                    {{ status }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="crm-label">Lead</label>
                            <select v-model="followUpForm.lead_id" class="crm-select">
                                <option value="">None</option>
                                <option v-for="lead in props.leads" :key="lead.id" :value="String(lead.id)">
                                    {{ lead.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="crm-label">Related task</label>
                            <select v-model="followUpForm.task_id" class="crm-select">
                                <option value="">None</option>
                                <option v-for="task in props.tasks" :key="task.id" :value="String(task.id)">
                                    {{ task.title }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="crm-label">Assign to</label>
                            <select v-model="followUpForm.assigned_to_user_id" class="crm-select">
                                <option value="">Unassigned</option>
                                <option v-for="member in props.teamMembers" :key="member.id" :value="String(member.id)">
                                    {{ member.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="crm-label">Notes</label>
                            <textarea v-model="followUpForm.notes" rows="4" class="crm-textarea"></textarea>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" :disabled="followUpForm.processing" class="crm-button-secondary">
                            {{ followUpForm.processing ? 'Saving...' : 'Create follow-up' }}
                        </button>
                    </div>
                </form>

                <div v-else class="crm-empty-state">
                    Your current role can view follow-ups, but cannot schedule them.
                </div>

                <article class="crm-card">
                    <h2 class="crm-panel-title">Upcoming follow-ups</h2>
                    <p class="crm-panel-copy">The next actions the team needs to take across active opportunities.</p>

                    <div class="mt-5 space-y-3">
                        <div v-for="followUp in props.followUps" :key="followUp.id" class="crm-card-soft">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-medium text-foreground">
                                        {{ followUp.leadName || followUp.taskTitle || 'General follow-up' }}
                                    </div>
                                    <div class="mt-1 text-sm text-muted-foreground">
                                        {{ followUp.scheduledFor || 'No schedule set' }}
                                        · {{ followUp.assignedTo || 'Unassigned' }}
                                    </div>
                                </div>
                                <span :class="statusClass(followUp.status)">
                                    {{ followUp.status || 'pending' }}
                                </span>
                            </div>
                            <div class="mt-3 text-sm text-muted-foreground">
                                {{ followUp.notes || 'No notes yet.' }}
                            </div>
                        </div>
                        <div v-if="props.followUps.length === 0" class="crm-empty-state">
                            No follow-ups scheduled yet.
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </div>
</template>
