<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { CalendarClock, CircleDashed, ListChecks, Loader } from 'lucide-vue-next';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/PageHeader.vue';
import SelectInput from '@/components/SelectInput.vue';
import StatCard from '@/components/StatCard.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
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
        onSuccess: () => {
            taskForm.reset('title', 'description', 'due_at');
            toast.success('Task created');
        },
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
        onSuccess: () => {
            followUpForm.reset('scheduled_for', 'notes');
            toast.success('Follow-up scheduled');
        },
    });
};
</script>

<template>
    <Head title="Tasks" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <PageHeader
            eyebrow="CRM"
            title="Tasks & follow-ups"
            description="Manage day-to-day sales execution, assign work to teammates, and schedule follow-up actions for leads."
        />

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Total tasks" :value="stats.totalTasks" :icon="ListChecks" />
            <StatCard label="Pending" :value="stats.pendingTasks" :icon="CircleDashed" />
            <StatCard label="In progress" :value="stats.inProgressTasks" :icon="Loader" />
            <StatCard label="Pending follow-ups" :value="stats.pendingFollowUps" :icon="CalendarClock" />
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <form v-if="canCreateTasks" class="rounded-2xl border border-border bg-card p-6" @submit.prevent="submitTask">
                    <h2 class="text-lg font-semibold">Create task</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Add an actionable item for a teammate or lead workflow.</p>

                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="task-title">Title</Label>
                            <Input id="task-title" v-model="taskForm.title" type="text" required :aria-invalid="Boolean(taskForm.errors.title)" />
                            <InputError :message="taskForm.errors.title" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="task-status">Status</Label>
                            <SelectInput id="task-status" v-model="taskForm.status">
                                <option v-for="status in taskStatusOptions" :key="status" :value="status">{{ status }}</option>
                            </SelectInput>
                        </div>
                        <div class="grid gap-2">
                            <Label for="task-priority">Priority</Label>
                            <SelectInput id="task-priority" v-model="taskForm.priority">
                                <option v-for="priority in taskPriorityOptions" :key="priority" :value="priority">{{ priority }}</option>
                            </SelectInput>
                        </div>
                        <div class="grid gap-2">
                            <Label for="task-due">Due at</Label>
                            <Input id="task-due" v-model="taskForm.due_at" type="datetime-local" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="task-lead">Lead</Label>
                            <SelectInput id="task-lead" v-model="taskForm.lead_id">
                                <option value="">None</option>
                                <option v-for="lead in leads" :key="lead.id" :value="String(lead.id)">{{ lead.name }}</option>
                            </SelectInput>
                        </div>
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="task-assignee">Assign to</Label>
                            <SelectInput id="task-assignee" v-model="taskForm.assigned_to_user_id">
                                <option value="">Unassigned</option>
                                <option v-for="member in teamMembers" :key="member.id" :value="String(member.id)">{{ member.name }}</option>
                            </SelectInput>
                        </div>
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="task-description">Description</Label>
                            <textarea
                                id="task-description"
                                v-model="taskForm.description"
                                rows="4"
                                class="border-input dark:bg-input/30 focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                            ></textarea>
                        </div>
                    </div>

                    <div class="mt-5">
                        <Button type="submit" :disabled="taskForm.processing">
                            <Spinner v-if="taskForm.processing" />
                            Create task
                        </Button>
                    </div>
                </form>

                <div v-else class="rounded-2xl border border-dashed border-border bg-card p-6 text-sm text-muted-foreground">
                    Your current role can view tasks, but cannot create new tasks. Contact a workspace admin for access.
                </div>

                <div class="rounded-2xl border border-border bg-card p-6">
                    <h2 class="text-lg font-semibold">Task list</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Open and recent tasks in this workspace.</p>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-b border-border text-muted-foreground">
                                <tr>
                                    <th class="px-3 py-3 font-medium">Title</th>
                                    <th class="px-3 py-3 font-medium">Status</th>
                                    <th class="px-3 py-3 font-medium">Priority</th>
                                    <th class="px-3 py-3 font-medium">Lead</th>
                                    <th class="px-3 py-3 font-medium">Assigned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="task in tasks" :key="task.id" class="border-b border-border/50 transition hover:bg-muted/50">
                                    <td class="px-3 py-3">
                                        <div class="font-medium">{{ task.title }}</div>
                                        <div class="text-xs text-muted-foreground">{{ task.dueAt || 'No due date' }}</div>
                                    </td>
                                    <td class="px-3 py-3"><StatusBadge :status="task.status" /></td>
                                    <td class="px-3 py-3 capitalize">{{ task.priority }}</td>
                                    <td class="px-3 py-3">{{ task.leadName || 'None' }}</td>
                                    <td class="px-3 py-3">{{ task.assignedTo || 'Unassigned' }}</td>
                                </tr>
                                <tr v-if="tasks.length === 0">
                                    <td colspan="5" class="px-3 py-10 text-center text-muted-foreground">
                                        No tasks yet. Create the first one from the form above.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <form v-if="canCreateFollowUps" class="rounded-2xl border border-border bg-card p-6" @submit.prevent="submitFollowUp">
                    <h2 class="text-lg font-semibold">Schedule follow-up</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Set the next touchpoint for a lead or task.</p>

                    <div class="mt-5 grid gap-4">
                        <div class="grid gap-2">
                            <Label for="followup-scheduled">Scheduled for</Label>
                            <Input id="followup-scheduled" v-model="followUpForm.scheduled_for" type="datetime-local" required :aria-invalid="Boolean(followUpForm.errors.scheduled_for)" />
                            <InputError :message="followUpForm.errors.scheduled_for" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="followup-status">Status</Label>
                            <SelectInput id="followup-status" v-model="followUpForm.status">
                                <option v-for="status in followUpStatusOptions" :key="status" :value="status">{{ status }}</option>
                            </SelectInput>
                        </div>
                        <div class="grid gap-2">
                            <Label for="followup-lead">Lead</Label>
                            <SelectInput id="followup-lead" v-model="followUpForm.lead_id">
                                <option value="">None</option>
                                <option v-for="lead in leads" :key="lead.id" :value="String(lead.id)">{{ lead.name }}</option>
                            </SelectInput>
                        </div>
                        <div class="grid gap-2">
                            <Label for="followup-task">Related task</Label>
                            <SelectInput id="followup-task" v-model="followUpForm.task_id">
                                <option value="">None</option>
                                <option v-for="task in tasks" :key="task.id" :value="String(task.id)">{{ task.title }}</option>
                            </SelectInput>
                        </div>
                        <div class="grid gap-2">
                            <Label for="followup-assignee">Assign to</Label>
                            <SelectInput id="followup-assignee" v-model="followUpForm.assigned_to_user_id">
                                <option value="">Unassigned</option>
                                <option v-for="member in teamMembers" :key="member.id" :value="String(member.id)">{{ member.name }}</option>
                            </SelectInput>
                        </div>
                        <div class="grid gap-2">
                            <Label for="followup-notes">Notes</Label>
                            <textarea
                                id="followup-notes"
                                v-model="followUpForm.notes"
                                rows="4"
                                class="border-input dark:bg-input/30 focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                            ></textarea>
                        </div>
                    </div>

                    <div class="mt-5">
                        <Button type="submit" variant="secondary" :disabled="followUpForm.processing">
                            <Spinner v-if="followUpForm.processing" />
                            Create follow-up
                        </Button>
                    </div>
                </form>

                <div v-else class="rounded-2xl border border-dashed border-border bg-card p-6 text-sm text-muted-foreground">
                    Your current role can view follow-ups, but cannot schedule them.
                </div>

                <div class="rounded-2xl border border-border bg-card p-6">
                    <h2 class="text-lg font-semibold">Upcoming follow-ups</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Scheduled touchpoints for your team.</p>

                    <div class="mt-5 space-y-3">
                        <div v-for="followUp in followUps" :key="followUp.id" class="rounded-xl border border-border p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-medium">{{ followUp.leadName || followUp.taskTitle || 'General follow-up' }}</div>
                                    <div class="mt-1 text-sm text-muted-foreground">{{ followUp.scheduledFor }} • {{ followUp.assignedTo || 'Unassigned' }}</div>
                                </div>
                                <StatusBadge :status="followUp.status" />
                            </div>
                            <div class="mt-2 text-sm text-muted-foreground">{{ followUp.notes || 'No notes yet.' }}</div>
                        </div>
                        <div v-if="followUps.length === 0" class="rounded-xl border border-dashed border-border p-4 text-sm text-muted-foreground">
                            No follow-ups scheduled yet.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
