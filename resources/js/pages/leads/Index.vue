<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Auth } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Leads',
                href: '/leads',
            },
        ],
    },
});

interface LeadRow {
    id: number;
    name: string;
    email: string | null;
    primaryPhone: string;
    status: string | null;
    source: string | null;
    assignedTo: string | null;
    createdAt: string | null;
}

interface LeadSourceOption {
    id: number;
    name: string;
    description: string | null;
}

interface TeamMemberOption {
    id: number;
    name: string;
}

const props = defineProps<{
    leads: LeadRow[];
    leadSources: LeadSourceOption[];
    teamMembers: TeamMemberOption[];
    leadStatusOptions: string[];
    stats: {
        total: number;
        new: number;
        contacted: number;
        qualified: number;
    };
}>();

const page = usePage<{ auth: Auth }>();
const permissions = computed(() => page.props.auth?.context?.permissions ?? []);
const canCreateLeads = computed(() => permissions.value.includes('leads.create'));

const leadForm = useForm({
    first_name: '',
    last_name: '',
    email: '',
    primary_phone: '',
    status: props.leadStatusOptions[0] ?? 'new',
    lead_source_id: '',
    assigned_to_user_id: '',
    notes: '',
});

const leadSourceForm = useForm({
    name: '',
    description: '',
});

const submitLead = () => {
    leadForm.transform((data) => ({
        ...data,
        lead_source_id: data.lead_source_id || null,
        assigned_to_user_id: data.assigned_to_user_id || null,
    })).post('/leads', {
        preserveScroll: true,
        onSuccess: () => leadForm.reset('first_name', 'last_name', 'email', 'primary_phone', 'notes'),
    });
};

const submitLeadSource = () => {
    leadSourceForm.post('/lead-sources', {
        preserveScroll: true,
        onSuccess: () => leadSourceForm.reset(),
    });
};
</script>

<template>
    <Head title="Leads" />

    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
        <section class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <p class="text-sm font-medium text-orange-500">Phase 2 CRM core</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Lead management foundation</h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 text-muted-foreground">
                Start capturing leads, tagging their source, and assigning ownership inside the current workspace.
            </p>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Total leads</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.total }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">New</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.new }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Contacted</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.contacted }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-background p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Qualified</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.qualified }}</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-6">
                <form
                    v-if="canCreateLeads"
                    class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border"
                    @submit.prevent="submitLead"
                >
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold">Create lead</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Add a lead and assign it to your team.</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium">First name</label>
                            <input v-model="leadForm.first_name" type="text" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500" />
                            <p v-if="leadForm.errors.first_name" class="mt-2 text-sm text-red-500">{{ leadForm.errors.first_name }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Last name</label>
                            <input v-model="leadForm.last_name" type="text" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500" />
                            <p v-if="leadForm.errors.last_name" class="mt-2 text-sm text-red-500">{{ leadForm.errors.last_name }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Phone</label>
                            <input v-model="leadForm.primary_phone" type="text" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500" />
                            <p v-if="leadForm.errors.primary_phone" class="mt-2 text-sm text-red-500">{{ leadForm.errors.primary_phone }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Email</label>
                            <input v-model="leadForm.email" type="email" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500" />
                            <p v-if="leadForm.errors.email" class="mt-2 text-sm text-red-500">{{ leadForm.errors.email }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Status</label>
                            <select v-model="leadForm.status" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option v-for="status in leadStatusOptions" :key="status" :value="status">{{ status }}</option>
                            </select>
                            <p v-if="leadForm.errors.status" class="mt-2 text-sm text-red-500">{{ leadForm.errors.status }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Lead source</label>
                            <select v-model="leadForm.lead_source_id" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option value="">None</option>
                                <option v-for="source in leadSources" :key="source.id" :value="String(source.id)">{{ source.name }}</option>
                            </select>
                            <p v-if="leadForm.errors.lead_source_id" class="mt-2 text-sm text-red-500">{{ leadForm.errors.lead_source_id }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Assign to</label>
                            <select v-model="leadForm.assigned_to_user_id" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500">
                                <option value="">Unassigned</option>
                                <option v-for="member in teamMembers" :key="member.id" :value="String(member.id)">{{ member.name }}</option>
                            </select>
                            <p v-if="leadForm.errors.assigned_to_user_id" class="mt-2 text-sm text-red-500">{{ leadForm.errors.assigned_to_user_id }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Notes</label>
                            <textarea v-model="leadForm.notes" rows="4" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500"></textarea>
                            <p v-if="leadForm.errors.notes" class="mt-2 text-sm text-red-500">{{ leadForm.errors.notes }}</p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" :disabled="leadForm.processing" class="inline-flex items-center rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-semibold text-zinc-950 transition hover:bg-orange-400 disabled:cursor-not-allowed disabled:opacity-70">
                            {{ leadForm.processing ? 'Saving...' : 'Create lead' }}
                        </button>
                    </div>
                </form>

                <div v-else class="rounded-2xl border border-dashed border-sidebar-border/70 bg-background p-6 text-sm text-muted-foreground dark:border-sidebar-border">
                    Your current role can view leads, but cannot create new leads.
                </div>

                <div class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold">Recent leads</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Newest leads in the current tenant workspace.</p>
                        </div>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border">
                                <tr>
                                    <th class="px-3 py-3 font-medium">Name</th>
                                    <th class="px-3 py-3 font-medium">Phone</th>
                                    <th class="px-3 py-3 font-medium">Status</th>
                                    <th class="px-3 py-3 font-medium">Source</th>
                                    <th class="px-3 py-3 font-medium">Assigned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="lead in leads" :key="lead.id" class="border-b border-sidebar-border/40 dark:border-sidebar-border/60">
                                    <td class="px-3 py-3">
                                        <div class="font-medium">{{ lead.name }}</div>
                                        <div class="text-xs text-muted-foreground">{{ lead.email || 'No email' }}</div>
                                    </td>
                                    <td class="px-3 py-3">{{ lead.primaryPhone }}</td>
                                    <td class="px-3 py-3">
                                        <span class="rounded-full bg-orange-500/10 px-2.5 py-1 text-xs font-medium text-orange-600 dark:text-orange-300">{{ lead.status }}</span>
                                    </td>
                                    <td class="px-3 py-3">{{ lead.source || 'Direct' }}</td>
                                    <td class="px-3 py-3">{{ lead.assignedTo || 'Unassigned' }}</td>
                                </tr>
                                <tr v-if="leads.length === 0">
                                    <td colspan="5" class="px-3 py-8 text-center text-muted-foreground">No leads yet. Create the first one from the form above.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <form v-if="canCreateLeads" class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border" @submit.prevent="submitLeadSource">
                    <h2 class="text-lg font-semibold">Add lead source</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Track where leads are coming from.</p>

                    <div class="mt-5 grid gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Source name</label>
                            <input v-model="leadSourceForm.name" type="text" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500" />
                            <p v-if="leadSourceForm.errors.name" class="mt-2 text-sm text-red-500">{{ leadSourceForm.errors.name }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Description</label>
                            <textarea v-model="leadSourceForm.description" rows="3" class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm outline-none transition focus:border-orange-500"></textarea>
                            <p v-if="leadSourceForm.errors.description" class="mt-2 text-sm text-red-500">{{ leadSourceForm.errors.description }}</p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" :disabled="leadSourceForm.processing" class="inline-flex items-center rounded-lg border border-sidebar-border/70 px-5 py-2.5 text-sm font-semibold transition hover:border-orange-500 dark:border-sidebar-border disabled:cursor-not-allowed disabled:opacity-70">
                            {{ leadSourceForm.processing ? 'Saving...' : 'Add source' }}
                        </button>
                    </div>
                </form>

                <div v-else class="rounded-2xl border border-dashed border-sidebar-border/70 bg-background p-6 text-sm text-muted-foreground dark:border-sidebar-border">
                    Lead source setup is only available to roles that can create leads.
                </div>

                <div class="rounded-2xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
                    <h2 class="text-lg font-semibold">Lead sources</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Available sources for this workspace.</p>

                    <div class="mt-5 space-y-3">
                        <div v-for="source in leadSources" :key="source.id" class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                            <div class="font-medium">{{ source.name }}</div>
                            <div class="mt-1 text-sm text-muted-foreground">{{ source.description || 'No description yet.' }}</div>
                        </div>
                        <div v-if="leadSources.length === 0" class="rounded-xl border border-dashed border-sidebar-border/70 p-4 text-sm text-muted-foreground dark:border-sidebar-border">
                            No lead sources yet.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
